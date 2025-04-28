import React, { useState } from 'react';
import Tabs from './Tabs';

const BuilderApp = ({ data }) => {
    const { item_id, item_type, item_title } = data;

    return (
        <div>
            <h2>{item_id ? `Edit Item: ${item_title}` : 'Create New Item'}</h2>
            <Tabs itemId={item_id} itemType={item_type} />
        </div>
    );
};

export default BuilderApp;
