import React, {Ref, useContext} from 'react';
import styled, {css} from 'styled-components';
import {AkeneoThemedProps, getColor, getColorForLevel, getFontSize, Level} from '../../theme';
import {LoadingContext, placeholderStyle} from 'shared/LoadingContext';

const BadgeContainer = styled.span<{load: boolean} & BadgeProps & AkeneoThemedProps>`
  display: inline-block;
  height: 18px;
  line-height: 16px;
  border: 1px solid;
  padding: 0 6px;
  border-radius: 2px;
  text-transform: uppercase;
  box-sizing: border-box;
  background-color: ${getColor('white')};
  font-size: ${getFontSize('small')};
  font-weight: normal;

  ${({level = 'primary'}: BadgeProps & AkeneoThemedProps) => css`
    color: ${getColorForLevel(level, 140)};
    border-color: ${getColorForLevel(level, 100)};
  `}

  ${props =>
    props.load
      ? css`
          ${placeholderStyle}
          color: transparent;
          min-width: 10px;
        `
      : ''}
`;

type BadgeProps = {
  /**
   * Level of the Badge defining it's color and outline.
   */
  level?: Level;

  /**
   * Children of the Badge, can only be string for a Badge.
   */
  children?: string;
};

/**
 * Badges are used for items that must be: tagged, categorized, organized by keywords, or to highlight information.
 */
const Badge = React.forwardRef<HTMLSpanElement, BadgeProps>(
  ({level = 'primary', children, ...rest}: BadgeProps, forwardedRef: Ref<HTMLSpanElement>) => {
    const loading = useContext(LoadingContext);

    return (
      <BadgeContainer level={level} ref={forwardedRef} load={loading} {...rest}>
        {children}
      </BadgeContainer>
    );
  }
);

export {Badge};
export type {BadgeProps};
