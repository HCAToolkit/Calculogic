import React from 'react';
import ReactDOM from 'react-dom';
import BuilderApp from './components/BuilderApp';

// Ensure WordPress data is available
if (typeof calculogicBuilderData === 'undefined') {
    console.error('calculogicBuilderData is not defined.');
} else {
    ReactDOM.render(
        <BuilderApp data={calculogicBuilderData} />,
        document.getElementById('calculogic-builder-app')
    );
}
