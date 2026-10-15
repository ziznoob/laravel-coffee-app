import { useState } from 'react';
import { api, formatPrice } from '../lib/api';

function CartItem({ item, processing, onQuantityChange, onRemove}){
    return (
         <li className="card shadow-sm" style={{ opacity: processing ? 0.6 : 1 }}>
            <div className="card-body d-flex gap-3">
                {item.image ? (
                    <img src={item.image} alt="" className="rounded object-fit-cover" width="80" height="80" />
                ) : (
                    <div className="rounded img-placeholder flex-shrink-0" style={{ width: 80, height: 80 }} aria-hidden="true" />
                )}

                <div className="flex-grow-1" style={{ minWidth: 0 }}>
                    <div className="d-flex justify-content-between gap-3">
                        <div>
                            <h3 className="h6 mb-1">{item.product_name}</h3>
                            <p className="small text-body-secondary mb-0">
                                {item.product_type} · {item.weight_label} · {formatPrice(item.unit_price)} each
                            </p>
                        </div>
                        <p className="fw-semibold text-nowrap mb-0">{formatPrice(item.line_total)}</p>
                    </div>

                    <div className="d-flex align-items-center justify-content-between mt-3">
                        <div className="input-group input-group-sm" style={{ width: 'auto' }} aria-label={`Quantity of ${item.product_name}`}>
                            <button
                                type="button"
                                onClick={() => onQuantityChange(item.quantity - 1)}
                                disabled={processing}
                                className="btn btn-outline-secondary"
                                aria-label="Decrease quantity"
                            >
                                −
                            </button>
                            <span className="input-group-text bg-white justify-content-center" style={{ width: 44 }} aria-live="polite">
                                {item.quantity}
                            </span>
                            <button
                                type="button"
                                onClick={() => onQuantityChange(item.quantity + 1)}
                                disabled={processing || item.quantity >= item.stock}
                                className="btn btn-outline-secondary"
                                aria-label="Increase quantity"
                            >
                                +
                            </button>
                        </div>

                        <button
                            type="button"
                            onClick={onRemove}
                            disabled={processing}
                            className="btn btn-link btn-sm text-danger"
                        >
                            Remove
                        </button>
                    </div>

                    {item.quantity >= item.stock && (
                        <p className="small text-warning-emphasis mt-2 mb-0">Maximum available stock reached.</p>
                    )}
                </div>
            </div>
        </li>
    );
}

export default function Cart({initialCart, itemsUrl, clearUrl, checkoutUrl, shopUrl}){
    const [cart, setCart] = useState(initialCart);
    const [pendingId, setPendingId] = useState(null);
    const [error, setError] = useState(null);

    async function send(id, url, options) {
        setPendingId(id);
        setError(null);

        try {
            const updated = await api(url, options);
            setCart(updated);
            updateHeaderCount(updated.count);
        } catch (e) {
            setError(e.message);
        } finally {
            setPendingId(null);
        }
    }

    async function submit () {
        setPendingId('all');
        setError(null);
        try {
            const { redirect } = await api(`${checkoutUrl}`,  { method: 'POST' });
            window.location.assign(redirect);
        }catch (e){
            setError(e.message);
            setPendingId(null);
        }
    }
    
    const changeQuantity = (item, quantity) => {
        if (quantity < 1) return removeItem(item);
        send(item.variant_id, `${itemsUrl}/${item.variant_id}`, { method: 'PATCH', body: { quantity } });
    };

    const removeItem = (item) =>
        send(item.variant_id, `${itemsUrl}/${item.variant_id}`, { method: 'DELETE' });

    const clearCart = () => {
        if (confirm('Remove everything from your cart?')) {
            send('all', clearUrl, { method: 'DELETE' });
        }
    };

    const checkout = () => {
        if (confirm('Confirm and Checkout?')){
            if(cart.items.length > 0){
                submit();
            }
        }
    }

    if (cart.items.length === 0) {
        return (
            <div className="card card-body shadow-sm text-center py-5">
                <h1 className="h3">Your cart is empty</h1>
                <p className="text-body-secondary">Find a coffee you'll love.</p>
                <div>
                    <a href={shopUrl} className="btn btn-dark">Browse coffee beans</a>
                </div>
            </div>
        );
    }

    return (
        <div>
            <div className="row flex-direction-column">
                <div className="col-lg-12">
                    <div className="d-flex align-items-baseline justify-content-between mb-4">
                        <h3 className="mb-0">Your Cart</h3>
                        <button
                            type="button"
                            onClick={clearCart}
                            disabled={pendingId !== null}
                            className="btn btn-link btn-sm text-danger"
                        >
                            Clear cart
                        </button>
                    </div>
                </div>

                {error && (
                    <div role="alert" className="alert alert-danger">
                        {error}
                    </div>
                )}

                <div className="col-lg-12">
                    <div className="row g-4">
                        <div className="col-lg-8">
                            <ul className="list-unstyled d-grid gap-3 mb-0">
                                {cart.items.map((item) => (
                                    <CartItem
                                        key={item.variant_id}
                                        item={item}
                                        processing={pendingId === item.variant_id || pendingId === 'all'}
                                        onQuantityChange={(qty) => changeQuantity(item, qty)}
                                        onRemove={() => removeItem(item)}
                                    />
                                ))}
                            </ul>
                        </div>

                        <div className="col-lg-4">
                            <div className="card card-body shadow-sm">
                                <h2 className="h5">Order summary</h2>
                                <dl className="mb-0 small">
                                    <div className="d-flex justify-content-between py-1">
                                        <dt className="fw-normal text-body-secondary">Items</dt>
                                        <dd className="mb-0">{cart.count}</dd>
                                    </div>
                                    <div className="d-flex justify-content-between border-top pt-2 fs-5 fw-semibold">
                                        <dt>Subtotal</dt>
                                        <dd className="mb-0">{formatPrice(cart.subtotal)}</dd>
                                    </div>
                                </dl>
                                <button
                                    type="button"
                                    disabled={cart.items.length === 0 || pendingId !== null}
                                    className="btn btn-dark w-100 mt-3"
                                    onClick={checkout}
                                >
                                    Checkout
                                </button>
                                <a href={shopUrl} className="d-block text-center small text-body-secondary mt-2">
                                    Continue shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

function updateHeaderCount(count) {
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        el.textContent = count;
        el.hidden = count === 0;
    });
}
