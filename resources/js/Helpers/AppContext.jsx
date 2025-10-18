import React, { createContext, useContext, useState } from 'react';

const AppStateContext = createContext();

export const AppStateProvider = ({ children }) => {
    const [appState, setAppState] = useState({
        theme: 'Aura',
        darkTheme: false,
    });

    const updateTheme = (theme) => {
        setAppState(prev => ({ ...prev, theme }));
    };

    const toggleDarkTheme = () => {
        setAppState(prev => ({ ...prev, darkTheme: !prev.darkTheme }));
    };

    return (
        <AppStateContext.Provider value={{ appState, updateTheme, toggleDarkTheme }}>
            {children}
        </AppStateContext.Provider>
    );
};

export const useAppState = () => {
    const context = useContext(AppStateContext);
    if (!context) {
        throw new Error('useAppState must be used within AppStateProvider');
    }
    return context;
};

