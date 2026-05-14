<?php

/**
 * Rental Controller
 * Handles rental operations
 */

require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Message.php';

class RentalController {

    private $rentalModel;
    private $bookModel;
    private $messageModel;

    public function __construct() {

        $this->rentalModel = new Rental();
        $this->bookModel = new Book();
        $this->messageModel = new Message();
    }

    /**
     * Send notification
     */
    private function sendNotification(
        $userId,
        $subject,
        $content
    ) {

        return $this->messageModel->create([
            'receiver_id' => $userId,
            'subject' => $subject,
            'content' => $content,
            'type' => 'system'
        ]);
    }

    /**
     * Create rental
     */
    public function create(
        $userId,
        $bookId,
        $rentalDays = 7
    ) {

        $userId = intval($userId);
        $bookId = intval($bookId);
        $rentalDays = intval($rentalDays);

        /**
         * Validate book ID
         */
        if ($bookId <= 0) {

            return [
                'success' => false,
                'message' => 'Invalid book ID'
            ];
        }

        /**
         * Validate rental duration
         */
        $allowedDays = [7, 14, 30];

        if (!in_array($rentalDays, $allowedDays)) {

            return [
                'success' => false,
                'message' => 'Invalid rental duration'
            ];
        }

        /**
         * Prevent duplicate active rentals
         */
        $existingRental = $this->rentalModel
            ->checkUserActiveRental(
                $userId,
                $bookId
            );

        if ($existingRental) {

            return [
                'success' => false,
                'message' => 'You are already renting this book'
            ];
        }

        /**
         * Find book
         */
        $book = $this->bookModel->findById($bookId);

        if (!$book) {

            return [
                'success' => false,
                'message' => 'Book not found'
            ];
        }

        /**
         * Check stock
         * IMPORTANT:
         * Change quantity -> stock
         * if your database uses stock
         */
        if ($book['quantity'] < 1) {

            return [
                'success' => false,
                'message' => 'Book is out of stock'
            ];
        }

        /**
         * Rental info
         */
        $rentalDate = date('Y-m-d');

        $dueDate = date(
            'Y-m-d',
            strtotime("+{$rentalDays} days")
        );

        $pickupDeadline = date(
            'Y-m-d',
            strtotime('+2 days')
        );

        /**
         * Rental code
         */
        $rentalCode = $this->rentalModel
            ->generateUniqueRentalCode();

        /**
         * Calculate price
         * IMPORTANT:
         * Change price_per_day
         * if your DB uses another name
         */
        $totalPrice =
            $book['price_per_day'] * $rentalDays;

        /**
         * Database transaction
         */
        try {

            $this->rentalModel
                ->getConnection()
                ->beginTransaction();

            /**
             * Create rental
             */
            $rentalId =
                $this->rentalModel->create([

                    'user_id' => $userId,
                    'book_id' => $bookId,
                    'rental_date' => $rentalDate,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                    'total_price' => $totalPrice,
                    'rental_code' => $rentalCode,
                    'pickup_deadline' => $pickupDeadline
                ]);

            if (!$rentalId) {

                throw new Exception(
                    'Failed to create rental'
                );
            }

            /**
             * Update stock
             */
            $updateStock =
                $this->bookModel
                    ->updateQuantity(
                        $bookId,
                        -1
                    );

            if (!$updateStock) {

                throw new Exception(
                    'Failed to update stock'
                );
            }

            /**
             * Send notification
             */
            $this->sendNotification(
                $userId,
                'Rental Created',
                'Your rental request has been created successfully.'
            );

            /**
             * Commit transaction
             */
            $this->rentalModel
                ->getConnection()
                ->commit();

            return [
                'success' => true,
                'message' => 'Rental created successfully',
                'rental_id' => $rentalId
            ];

        } catch (Exception $e) {

            /**
             * Rollback transaction
             */
            $this->rentalModel
                ->getConnection()
                ->rollBack();

            error_log($e->getMessage());

            return [
                'success' => false,
                'message' => 'Rental creation failed'
            ];
        }
    }

    /**
     * Return rented book
     */
    public function returnBook(
        $rentalId,
        $userId
    ) {

        $rentalId = intval($rentalId);
        $userId = intval($userId);

        /**
         * Find rental
         */
        $rental = $this->rentalModel
            ->findById($rentalId);

        if (!$rental) {

            return [
                'success' => false,
                'message' => 'Rental not found'
            ];
        }

        /**
         * Ownership validation
         */
        if ($rental['user_id'] != $userId) {

            return [
                'success' => false,
                'message' => 'Unauthorized action'
            ];
        }

        try {

            $this->rentalModel
                ->getConnection()
                ->beginTransaction();

            /**
             * Return process
             */
            $result =
                $this->rentalModel
                    ->returnBook($rentalId);

            if (!$result) {

                throw new Exception(
                    'Return process failed'
                );
            }

            /**
             * Restore stock
             */
            $updateStock =
                $this->bookModel
                    ->updateQuantity(
                        $rental['book_id'],
                        1
                    );

            if (!$updateStock) {

                throw new Exception(
                    'Failed to restore stock'
                );
            }

            /**
             * Send notification
             */
            $this->sendNotification(
                $userId,
                'Book Returned',
                'Your book has been returned successfully.'
            );

            /**
             * Commit
             */
            $this->rentalModel
                ->getConnection()
                ->commit();

            return [
                'success' => true,
                'message' => 'Book returned successfully'
            ];

        } catch (Exception $e) {

            /**
             * Rollback
             */
            $this->rentalModel
                ->getConnection()
                ->rollBack();

            error_log($e->getMessage());

            return [
                'success' => false,
                'message' => 'Return process failed'
            ];
        }
    }
}