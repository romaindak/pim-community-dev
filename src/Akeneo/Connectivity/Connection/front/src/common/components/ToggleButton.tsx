import React, {DetailedHTMLProps, forwardRef, InputHTMLAttributes, Ref} from 'react';
import {useTranslate} from '../../shared/translate';

type InputProps = DetailedHTMLProps<InputHTMLAttributes<HTMLInputElement>, HTMLInputElement>;

export const ToggleButton = forwardRef((props: InputProps, ref: Ref<HTMLInputElement>) => {
    const translate = useTranslate();

    return (
        <div className='switch switch-small has-switch' data-on-label='Yes' data-off-label='No'>
            <div className={`switch-animate switch-${props.defaultChecked ? 'on' : 'off'}`}>
                <input type='checkbox' ref={ref} {...props} id={props.id || props.name} />
                <span className='switch-left switch-small'>{translate('Yes')}</span>
                <label className='switch-small' htmlFor={props.id || props.name}>
                    &nbsp;
                </label>
                <span className='switch-right switch-small'>{translate('No')}</span>
            </div>
        </div>
    );
});
