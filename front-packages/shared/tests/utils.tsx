import React, {FC, ReactElement} from 'react';
import ReactDOM from 'react-dom';
import {ThemeProvider} from 'styled-components';
import {render} from '@testing-library/react';
import {renderHook} from '@testing-library/react-hooks';
import {pimTheme} from 'akeneo-design-system';
import {DependenciesContext} from '../src/DependenciesContext';
import {mockedDependencies} from './mockedDependencies';

const DefaultProviders: FC = ({children}) => (
  <DependenciesContext.Provider value={mockedDependencies}>
    <ThemeProvider theme={pimTheme}>{children}</ThemeProvider>
  </DependenciesContext.Provider>
);

const renderWithProviders = (ui: ReactElement) => render(ui, {wrapper: DefaultProviders});

const renderDOMWithProviders = (ui: ReactElement, container: HTMLElement) =>
  ReactDOM.render(<DefaultProviders>{ui}</DefaultProviders>, container);

const renderHookWithProviders = (hook: () => any) => renderHook(hook, {wrapper: DefaultProviders});

export {renderWithProviders, renderDOMWithProviders, renderHookWithProviders};
