// mounts components into <div data-react="Name" data-props="{...}"> in Blade views
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import Cart from './components/Cart';

const components = {
    Cart,
};

document.querySelectorAll('[data-react]').forEach((el) => {
    const Component = components[el.dataset.react];

    if (!Component) {
        console.error(`React component "${el.dataset.react}" is not registered in react-app.jsx`);
        return;
    }

    const props = JSON.parse(el.dataset.props || '{}');

    createRoot(el).render(
        <StrictMode>
            <Component {...props} />
        </StrictMode>,
    );
});
