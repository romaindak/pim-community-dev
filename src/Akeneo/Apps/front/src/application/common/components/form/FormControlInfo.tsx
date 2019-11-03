import React, {PropsWithChildren} from 'react';
import styled from 'styled-components';
import infoIconUrl from '../../assets/icons/info.svg';

export const FormControlInfo = ({children}: PropsWithChildren<{}>) => (
    <MessageWithIcon url={infoIconUrl}>{children}</MessageWithIcon>
);

const MessageWithIcon = styled.div<{url: string}>`
    display: flex;
    align-items: baseline;
    margin-top: 6px;
    color: #67768a;
    background: url(${props => props.url}) no-repeat left center;
    padding-left: 26px;

    a {
        color: #5992c7;
        text-decoration: underline;
        font-weight: 700;
    }
`;
