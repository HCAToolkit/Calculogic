import React, { useState } from 'react';
import BuildTab from './tabs/BuildTab';
import CalculogicTab from './tabs/CalculogicTab';
import ViewTab from './tabs/ViewTab';
import KnowledgeTab from './tabs/KnowledgeTab';
import ResultsTab from './tabs/ResultsTab';

const Tabs = ({ itemId, itemType }) => {
    const [activeTab, setActiveTab] = useState('build');

    const renderTabContent = () => {
        switch (activeTab) {
            case 'build':
                return <BuildTab itemId={itemId} />;
            case 'calculogic':
                return <CalculogicTab itemId={itemId} />;
            case 'view':
                return <ViewTab itemId={itemId} />;
            case 'knowledge':
                return <KnowledgeTab itemId={itemId} />;
            case 'results':
                return <ResultsTab itemId={itemId} />;
            default:
                return null;
        }
    };

    return (
        <div>
            <ul className="tab-navigation">
                <li onClick={() => setActiveTab('build')}>Build</li>
                <li onClick={() => setActiveTab('calculogic')}>Calculogic</li>
                <li onClick={() => setActiveTab('view')}>View</li>
                <li onClick={() => setActiveTab('knowledge')}>Knowledge</li>
                <li onClick={() => setActiveTab('results')}>Results</li>
            </ul>
            <div className="tab-content">{renderTabContent()}</div>
        </div>
    );
};

export default Tabs;
