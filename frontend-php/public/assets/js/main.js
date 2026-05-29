document.addEventListener('DOMContentLoaded', ()=>{
  // add to cart buttons
  document.querySelectorAll('[data-add-to-cart]').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
      e.preventDefault();
      const id = btn.getAttribute('data-id');
      const res = await fetch('/frontend-php/api/add_to_cart.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id, qty:1})});
      const data = await res.json();
      if(data.success){
        const count = document.getElementById('cart-count');
        if(count) count.textContent = data.count;
        toast('Added to cart');
      } else toast('Error');
    });
  });
});
function toast(msg){
  const el = document.createElement('div');
  el.textContent = msg;
  el.className = 'fixed bottom-6 right-6 bg-white text-black px-4 py-2 rounded shadow-lg';
  document.body.appendChild(el);
  setTimeout(()=> el.remove(), 2000);
}
