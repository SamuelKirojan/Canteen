<?php
// Home page content migrated from index.html (body content only)
?>

<!-- ======= Hero Section ======= -->
<section id="hero" class="hero d-flex align-items-center section-bg">
  <div class="container">
    <div class="row justify-content-between gy-5">
      <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center align-items-center align-items-lg-start text-center text-lg-start">
        <h2 data-aos="fade-up">Sehat Lezat Enak<br>Unklab Canteen</h2>
        <p data-aos="fade-up" data-aos-delay="100">Kami menyediakan Makanan Pembuka, Makan Siang, Minuman dan Snacks, menemani hari anda dalam beraktivitas di Universitas Klabat.</p>
        <div data-aos="fade-up" data-aos-delay="150">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="index.php?r=menu/index" class="btn btn-primary btn-lg mt-3">Order Now</a>
          <?php else: ?>
            <a href="index.php?r=auth/account" class="btn btn-primary btn-lg mt-3">Order Now</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-5 order-1 order-lg-2 text-center text-lg-start">
        <img src="<?php echo isset($baseUrl) ? $baseUrl : ''; ?>assets/img/chef.jpg" class="img-fluid" alt="" data-aos="zoom-out" data-aos-delay="300">
      </div>
    </div>
  </div>
</section><!-- End Hero Section -->

<main id="main">
  <section>
    <style>
      .modal-backdrop.show { backdrop-filter: blur(6px); background-color: rgba(0,0,0,0.25); }
      .menu-card img { object-fit: cover; height: 180px; width: 100%; }
    </style>

    <div class="modal fade" id="menuModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Menu <?php if (isset($items)) { echo '(' . (int)count($items) . ')'; } ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if (!empty($dbError)): ?>
              <div class="alert alert-danger" role="alert">
                Database error: <?php echo htmlspecialchars($dbError); ?>
              </div>
            <?php endif; ?>
            <div class="row g-4">
              <?php foreach (($items ?? []) as $item): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                  <div class="card menu-card h-100">
                    <?php if (!empty($item['image_url'])): ?>
                      <?php $img=$item['image_url']; if (!preg_match('#^https?://|^/#',$img)) { $img=ltrim($img,'/'); } ?>
                      <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                      <h6 class="card-title mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                      <?php if ($item['price'] !== null): ?>
                        <div class="text-primary fw-semibold mb-1">Rp <?php echo number_format((float)$item['price'] * 1000, 0, ',', '.'); ?></div>
                      <?php endif; ?>
                      <?php if ($item['stock'] !== null): ?>
                        <div class="text-muted small mb-2">Stock: <?php echo (int)$item['stock']; ?></div>
                      <?php endif; ?>
                      <?php if (!empty($item['description'])): ?>
                        <p class="small flex-grow-1 mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                      <?php endif; ?>
                      <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm flex-nowrap" style="width: 130px;">
                          <button class="btn btn-outline-secondary btn-qty-dec" type="button" disabled>-</button>
                          <input type="number" class="form-control text-center qty-input" value="1" min="1" step="1">
                          <button class="btn btn-outline-secondary btn-qty-inc" type="button">+</button>
                        </div>
                        <button class="btn btn-sm btn-primary flex-grow-1 btn-add-to-cart" data-item-id="<?php echo (int)$item['id']; ?>" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" data-item-price="<?php echo htmlspecialchars((float)$item['price']); ?>" data-item-image="<?php echo htmlspecialchars($img ?? ''); ?>">Add to Cart</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
              <?php if (empty($items)): ?>
                <div class="col-12 text-center text-muted">No menu items found.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <style>
      .floating-btn { position: fixed; left: 20px; bottom: 20px; z-index: 1085; width: 56px; height: 56px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; }
      .cart-badge { position: absolute; top: -6px; right: -6px; }
      .pulse { animation: pulse 0.4s ease; }
      @keyframes pulse { 0%{transform:scale(1);} 50%{transform:scale(1.1);} 100%{transform:scale(1);} }
      .modal { z-index: 1080; }
    </style>

    <button id="btnCart" type="button" class="btn btn-primary rounded-circle p-3 floating-btn position-relative" data-bs-toggle="modal" data-bs-target="#checkoutModal" aria-label="Cart" style="display:none;">
      <i class="bi bi-bag"></i>
      <span id="cartCount" class="badge bg-danger rounded-pill cart-badge">0</span>
    </button>

    

    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index:1090">
      <div id="toastAdded" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">Menu added to Cart</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
      <div id="toastOrder" class="toast align-items-center text-white bg-primary border-0 mt-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">Order placed</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="checkoutTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-checkout" data-bs-toggle="tab" data-bs-target="#pane-checkout" type="button" role="tab">Checkout</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link d-none" id="tab-orderinfo" data-bs-toggle="tab" data-bs-target="#pane-orderinfo" type="button" role="tab">Order Info</button>
              </li>
            </ul>
            <div class="tab-content pt-3">
              <div class="tab-pane fade show active" id="pane-checkout" role="tabpanel">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Items</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#menuModal">Add other menu</button>
              </div>
              <div id="cartList" class="list-group mb-3"></div>
              <div class="d-flex justify-content-between align-items-center border-top pt-2">
                <div class="fw-semibold">Total</div>
                <div id="cartTotal" class="fw-bold">Rp 0</div>
              </div>
              <hr>
              <div class="mb-2">Order Type</div>
              <div class="btn-group mb-3" role="group">
                <input type="radio" class="btn-check" name="orderType" id="orderPickup" autocomplete="off" checked>
                <label class="btn btn-outline-secondary" for="orderPickup">Pick up</label>
                <input type="radio" class="btn-check" name="orderType" id="orderDelivery" autocomplete="off">
                <label class="btn btn-outline-secondary" for="orderDelivery">Deliver</label>
              </div>
              <div id="pickupForm" class="row g-3">
                <div class="col-12">
                  <input id="pickupName" type="text" class="form-control" placeholder="Name">
                </div>
              </div>
              <div id="deliveryForm" class="row g-3" style="display:none;">
                <div class="col-md-6">
                  <input id="deliveryName" type="text" class="form-control" placeholder="Name">
                </div>
                <div class="col-md-6">
                  <input id="deliveryPhone" type="text" class="form-control" placeholder="Phone Number">
                </div>
                <div class="col-12">
                  <input id="deliveryAddress" type="text" class="form-control" placeholder="Address">
                </div>
                <div class="col-md-6">
                  <input id="deliveryCost" type="text" class="form-control" placeholder="Delivery Cost (placeholder)">
                </div>
              </div>
              </div>
              <div class="tab-pane fade" id="pane-orderinfo" role="tabpanel">
                <div id="orderInfoContent" class="text-muted">No order yet.</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="btnPlaceOrder" type="button" class="btn btn-primary">Place Order</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function(){
        const cartKey = 'canteen_cart_v1';
        const lastOrderKey = 'canteen_last_order_v1';
        let cart = JSON.parse(localStorage.getItem(cartKey) || '[]');
        const fmt = n => new Intl.NumberFormat('id-ID').format(n);
        const cartCountEl = document.getElementById('cartCount');
        const btnCart = document.getElementById('btnCart');
        const btnOrderInfo = document.getElementById('btnOrderInfo');
        const cartList = document.getElementById('cartList');
        const cartTotal = document.getElementById('cartTotal');
        const checkoutModal = document.getElementById('checkoutModal');
        const menuModal = document.getElementById('menuModal');
        const toastAddedEl = document.getElementById('toastAdded');
        const toastOrderEl = document.getElementById('toastOrder');
        function showToast(el){ if(window.bootstrap && el){ new bootstrap.Toast(el).show(); } }
        const tabCheckoutBtn = document.getElementById('tab-checkout');
        const tabOrderInfoBtn = document.getElementById('tab-orderinfo');

        function saveCart(){ localStorage.setItem(cartKey, JSON.stringify(cart)); }
        function updateBadge(){
          const c = cart.reduce((s,i)=>s+i.qty,0);
          cartCountEl.textContent = c;
          btnCart.style.display = c>0 ? 'inline-flex' : 'none';
        }
        function addAnimation(el){ el.classList.add('pulse'); setTimeout(()=>el.classList.remove('pulse'),300); }
        function renderCart(){
          cartList.innerHTML = '';
          if(cart.length===0){
            cartList.innerHTML = '<div class="list-group-item text-muted">Cart is empty</div>';
            cartTotal.textContent = 'Rp 0';
            return;
          }
          let total = 0;
          cart.forEach((item,idx)=>{
            const line = document.createElement('div');
            line.className = 'list-group-item';
            const lineTotal = item.qty * item.price * 1000;
            total += lineTotal;
            line.innerHTML = `
              <div class="d-flex align-items-center">
                <img src="${item.image||''}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;" class="me-3">
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between">
                    <strong>${item.name}</strong>
                    <span>Rp ${fmt(item.price*1000)}</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="input-group input-group-sm" style="width: 140px;">
                      <button class="btn btn-outline-secondary btn-line-dec" data-idx="${idx}" ${item.qty<=1?'disabled':''}>-</button>
                      <input type="number" class="form-control text-center line-qty" data-idx="${idx}" value="${item.qty}" min="1">
                      <button class="btn btn-outline-secondary btn-line-inc" data-idx="${idx}">+</button>
                    </div>
                    <div class="ms-3">Rp ${fmt(lineTotal)}</div>
                    <button class="btn btn-sm btn-link text-danger ms-2 btn-remove" data-idx="${idx}"><i class="bi bi-trash"></i></button>
                  </div>
                </div>
              </div>`;
            cartList.appendChild(line);
          });
          cartTotal.textContent = 'Rp ' + fmt(total);
        }

        function upsertToCart(data){
          const idx = cart.findIndex(x=>x.id===data.id);
          if(idx>=0){ cart[idx].qty += data.qty; } else { cart.push(data); }
          saveCart(); updateBadge(); addAnimation(btnCart); showToast(toastAddedEl);
        }

        document.addEventListener('click', function(e){
          if(e.target.closest('.btn-qty-inc')){
            e.preventDefault();
            e.stopPropagation();
            const group = e.target.closest('.input-group');
            const input = group.querySelector('.qty-input');
            input.value = (+input.value||1)+1; group.querySelector('.btn-qty-dec').disabled = (+input.value)<=1;
          }
          if(e.target.closest('.btn-qty-dec')){
            e.preventDefault();
            e.stopPropagation();
            const group = e.target.closest('.input-group');
            const input = group.querySelector('.qty-input');
            const v = Math.max(1,(+input.value||1)-1); input.value = v; group.querySelector('.btn-qty-dec').disabled = v<=1;
          }
          if(e.target.closest('.btn-add-to-cart')){
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.btn-add-to-cart');
            const card = btn.closest('.card');
            const qty = Math.max(1, +card.querySelector('.qty-input').value || 1);
            const item = { id: +btn.dataset.itemId, name: btn.dataset.itemName, price: +btn.dataset.itemPrice, image: btn.dataset.itemImage, qty };
            upsertToCart(item);
          }

          if(e.target.closest('.btn-line-inc')){
            const idx = +e.target.dataset.idx; cart[idx].qty++; saveCart(); renderCart(); updateBadge();
          }
          if(e.target.closest('.btn-line-dec')){
            const idx = +e.target.dataset.idx; cart[idx].qty = Math.max(1, cart[idx].qty-1); saveCart(); renderCart(); updateBadge();
          }
          if(e.target.closest('.btn-remove')){
            const idx = +e.target.dataset.idx; cart.splice(idx,1); saveCart(); renderCart(); updateBadge();
          }
        });

        document.addEventListener('input', function(e){
          if(e.target.classList.contains('qty-input')){
            const dec = e.target.closest('.input-group').querySelector('.btn-qty-dec');
            const v = Math.max(1, +e.target.value||1); e.target.value = v; dec.disabled = v<=1;
          }
          if(e.target.classList.contains('line-qty')){
            const idx = +e.target.dataset.idx; const v = Math.max(1, +e.target.value||1); cart[idx].qty = v; saveCart(); renderCart(); updateBadge();
          }
        });

        document.getElementById('orderPickup').addEventListener('change', function(){
          document.getElementById('pickupForm').style.display = 'block';
          document.getElementById('deliveryForm').style.display = 'none';
        });
        document.getElementById('orderDelivery').addEventListener('change', function(){
          document.getElementById('pickupForm').style.display = 'none';
          document.getElementById('deliveryForm').style.display = 'flex';
        });

        checkoutModal.addEventListener('show.bs.modal', function(){
          renderCart();
          if (!tabOrderInfoBtn.classList.contains('d-none')) {
            new bootstrap.Tab(tabCheckoutBtn).show();
          } else {
            new bootstrap.Tab(tabCheckoutBtn).show();
          }
        });

        document.getElementById('btnPlaceOrder').addEventListener('click', function(){
          if(cart.length===0){ return; }
          const isDelivery = document.getElementById('orderDelivery').checked;
          const order = {
            ts: Date.now(),
            type: isDelivery ? 'delivery' : 'pickup',
            items: cart.slice(),
            name: isDelivery ? document.getElementById('deliveryName').value : document.getElementById('pickupName').value,
            phone: isDelivery ? document.getElementById('deliveryPhone').value : '',
            address: isDelivery ? document.getElementById('deliveryAddress').value : '',
            deliveryCost: isDelivery ? document.getElementById('deliveryCost').value : '',
            status: isDelivery ? 'Packing' : 'Ready'
          };
          localStorage.setItem(lastOrderKey, JSON.stringify(order));
          showToast(toastOrderEl);
          const total = order.items.reduce((s,i)=>s+i.qty*i.price*1000,0);
          const rows = order.items.map(i=>`<tr><td>${i.name}</td><td class="text-end">${i.qty}</td><td class="text-end">Rp ${fmt(i.price*1000)}</td><td class="text-end">Rp ${fmt(i.qty*i.price*1000)}</td></tr>`).join('');
          const extra = order.type==='delivery' ? `<div class=\"mt-2\">Delivery Cost: ${order.deliveryCost||'-'}</div><div>Delivery Status: ${order.status}</div>` : '';
          const infoHtml = `
            <div>Type: ${order.type==='delivery'?'Delivery':'Pick up'}</div>
            <div>Name: ${order.name||'-'}</div>
            ${order.type==='delivery'?`<div>Phone: ${order.phone||'-'}</div><div>Address: ${order.address||'-'}</div>`:''}
            <div class='table-responsive mt-3'>
              <table class='table table-sm'>
                <thead><tr><th>Item</th><th class='text-end'>Qty</th><th class='text-end'>Price</th><th class='text-end'>Subtotal</th></tr></thead>
                <tbody>${rows}</tbody>
                <tfoot><tr><th colspan='3' class='text-end'>Total</th><th class='text-end'>Rp ${fmt(total)}</th></tr></tfoot>
              </table>
            </div>
            ${extra}`;
          document.getElementById('orderInfoContent').innerHTML = infoHtml;
          tabOrderInfoBtn.classList.remove('d-none');
          new bootstrap.Tab(tabOrderInfoBtn).show();
          cart = []; saveCart(); updateBadge();
        });

        document.getElementById('btnToggleOrderInfo').addEventListener('click', function(){
          const data = localStorage.getItem(lastOrderKey); if(!data){ return; }
          const order = JSON.parse(data);
          const total = order.items.reduce((s,i)=>s+i.qty*i.price*1000,0);
          const rows = order.items.map(i=>`<tr><td>${i.name}</td><td class='text-end'>${i.qty}</td><td class='text-end'>Rp ${fmt(i.price*1000)}</td><td class='text-end'>Rp ${fmt(i.qty*i.price*1000)}</td></tr>`).join('');
          const extra = order.type==='delivery' ? `<div class='mt-2'>Delivery Cost: ${order.deliveryCost||'-'}</div><div>Delivery Status: ${order.status}</div>` : '';
          document.getElementById('orderInfoContent').innerHTML = `
            <div>Type: ${order.type==='delivery'?'Delivery':'Pick up'}</div>
            <div>Name: ${order.name||'-'}</div>
            ${order.type==='delivery'?`<div>Phone: ${order.phone||'-'}</div><div>Address: ${order.address||'-'}</div>`:''}
            <div class='table-responsive mt-3'>
              <table class='table table-sm'>
                <thead><tr><th>Item</th><th class='text-end'>Qty</th><th class='text-end'>Price</th><th class='text-end'>Subtotal</th></tr></thead>
                <tbody>${rows}</tbody>
                <tfoot><tr><th colspan='3' class='text-end'>Total</th><th class='text-end'>Rp ${fmt(total)}</th></tr></tfoot>
              </table>
            </div>
            ${extra}`;
          document.getElementById('pane-checkout').style.display = 'none';
          document.getElementById('pane-orderinfo').style.display = 'block';
          this.classList.add('d-none');
        });

        document.getElementById('btnBackToCheckout').addEventListener('click', function(){
          document.getElementById('pane-orderinfo').style.display = 'none';
          document.getElementById('pane-checkout').style.display = 'block';
          document.getElementById('btnToggleOrderInfo').classList.toggle('d-none', !localStorage.getItem(lastOrderKey));
        });

        function init(){
          updateBadge();
          if(cart.length>0){ btnCart.style.display='inline-flex'; }
          if(localStorage.getItem(lastOrderKey)) { tabOrderInfoBtn.classList.remove('d-none'); }
        }
        init();
      })();
    </script>
  </section>

  <!-- ======= Contact Section ======= -->
  <section id="contact" class="contact">
    <div class="container" data-aos="fade-up">

      <div class="section-header d-flex justify-content-between align-items-center">
        <div>
          <h2>Contact</h2>
          <p>Need Help? <span>Contact Us</span></p>
        </div>
      </div>

      <div class="mb-3">
        <iframe style="border:0; width: 100%; height: 350px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d997.149158123655!2d124.98266649999998!3d1.4179218!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32870fb08562b233%3A0x39f6f555cdb6eeab!2sStudent%20Canteen%20Universitas%20Klabat!5e0!3m2!1sid!2sid!4v1698071157842!5m2!1sid!2sid" frameborder="0" allowfullscreen></iframe>
      </div><!-- End Google Maps -->

      <div class="row gy-4">

        <div class="col-md-6">
          <div class="info-item  d-flex align-items-center">
            <i class="icon bi bi-map flex-shrink-0"></i>
            <div>
              <h3>Our Address</h3>
              <p>Student Canteen Unklab, depan Asrama Jasmine, Universitas Klabat.</p>
            </div>
          </div>
        </div><!-- End Info Item -->

        <div class="col-md-6">
          <div class="info-item d-flex align-items-center">
            <i class="icon bi bi-envelope flex-shrink-0"></i>
            <div>
              <h3>Email Us</h3>
              <p>info@unklab.ac.id</p>
            </div>
          </div>
        </div><!-- End Info Item -->

        <div class="col-md-6">
          <div class="info-item  d-flex align-items-center">
            <i class="icon bi bi-telephone flex-shrink-0"></i>
            <div>
              <h3>Call Us</h3>
              <p>+62431 891036</p>
            </div>
          </div>
        </div><!-- End Info Item -->

        <div class="col-md-6">
          <div class="info-item  d-flex align-items-center">
            <i class="icon bi bi-share flex-shrink-0"></i>
            <div>
              <h3>Opening Hours</h3>
              <div><strong>Mon-Fri</strong> 07AM - 18PM;
                <strong>Saturday-Sunday:</strong> Closed
              </div>
            </div>
          </div>
        </div><!-- End Info Item -->

      </div>    
    </div>
  </section><!-- End Contact Section -->
</main>
