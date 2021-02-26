import React from 'react';
import {useTranslate, useRoute} from '@akeneo-pim-community/legacy-bridge';
import {filterErrors, formatParameters} from '@akeneo-pim-community/shared';
import {
  BrokenLinkIcon,
  EditIcon,
  CloseIcon,
  useTheme,
  Helper,
  Badge,
  Table,
  Image,
  NumberInput,
  IconButton,
} from 'akeneo-design-system';
import {ProductType, Row, QuantifiedLink, MAX_QUANTITY} from '../models';
import {useProductThumbnail} from '../hooks';

type QuantifiedAssociationRowProps = {
  row: Row;
  parentQuantifiedLink: QuantifiedLink | undefined;
  isCompact?: boolean;
  onChange: (row: Row) => void;
  onRemove: (row: Row) => void;
};

const QuantifiedAssociationRow = ({
  row,
  parentQuantifiedLink,
  isCompact = false,
  onChange,
  onRemove,
}: QuantifiedAssociationRowProps) => {
  const translate = useTranslate();
  const isProductModel = ProductType.ProductModel === row.productType;
  const productEditUrl = useRoute(`pim_enrich_${row.productType}_edit`, {id: row.product?.id.toString() || ''});
  const thumbnailUrl = useProductThumbnail(row.product);
  const blueColor = useTheme().color.blue100;

  return null === row.product ? (
    <Table.Row>
      <Table.Cell className="AknLoadingPlaceHolderContainer" colSpan={6} />
    </Table.Row>
  ) : (
    <Table.Row>
      <Table.Cell>
        <Image width={44} height={44} src={thumbnailUrl} alt={row.product.label} isStacked={isProductModel} />
      </Table.Cell>
      <Table.Cell rowTitle={!isProductModel}>{row.product.label}</Table.Cell>
      <Table.Cell>{row.quantifiedLink.identifier}</Table.Cell>
      {!isCompact && (
        <Table.Cell>
          {null === row.product.completeness ? (
            translate('pim_common.not_available')
          ) : (
            <Badge>{row.product.completeness}%</Badge>
          )}
        </Table.Cell>
      )}
      {!isCompact && (
        <Table.Cell>
          {null === row.product.variant_product_completenesses ? (
            translate('pim_common.not_available')
          ) : (
            <Badge>
              {row.product.variant_product_completenesses.completeChildren} /{' '}
              {row.product.variant_product_completenesses.totalChildren}
            </Badge>
          )}
        </Table.Cell>
      )}
      <Table.Cell>
        <NumberInput
          title={translate('pim_enrich.entity.product.module.associations.quantified.quantity')}
          type="number"
          min={1}
          max={MAX_QUANTITY}
          value={row.quantifiedLink.quantity.toString()}
          invalid={0 < filterErrors(row.errors, 'quantity').length}
          onChange={number => {
            const numberValue = Number(number) || 1;
            const limitedValue = numberValue > MAX_QUANTITY ? row.quantifiedLink.quantity : numberValue;

            onChange({
              ...row,
              quantifiedLink: {...row.quantifiedLink, quantity: limitedValue},
            });
          }}
        />
        {formatParameters(row.errors).map((error, key) => (
          <Helper key={key} level="error" inline={true}>
            {translate(error.messageTemplate, error.parameters, error.plural)}
          </Helper>
        ))}
      </Table.Cell>
      {!isCompact ? (
        <Table.Cell>
          {undefined !== parentQuantifiedLink && parentQuantifiedLink.quantity !== row.quantifiedLink.quantity && (
            <BrokenLinkIcon
              color={blueColor}
              title={translate('pim_enrich.entity.product.module.associations.quantified.unlinked')}
            />
          )}
          {null !== row.product && (
            <IconButton
              level="tertiary"
              ghost="borderless"
              href={`#${productEditUrl}`}
              target="_blank"
              title={translate('pim_enrich.entity.product.module.associations.edit')}
              icon={<EditIcon />}
            />
          )}
          {undefined === parentQuantifiedLink && (
            <IconButton
              level="tertiary"
              ghost="borderless"
              onClick={() => onRemove(row)}
              title={translate('pim_enrich.entity.product.module.associations.remove')}
              icon={<CloseIcon />}
            />
          )}
        </Table.Cell>
      ) : (
        <Table.Cell>
          {undefined === parentQuantifiedLink && (
            <IconButton
              level="tertiary"
              ghost="borderless"
              onClick={() => onRemove(row)}
              title={translate('pim_enrich.entity.product.module.associations.remove')}
              icon={<CloseIcon />}
            />
          )}
        </Table.Cell>
      )}
    </Table.Row>
  );
};

export {QuantifiedAssociationRow};
