import React, {ReactNode} from 'react';
import {Button, DeleteIllustration, Modal} from 'akeneo-design-system';
import {useTranslate} from '../hooks';

type DeleteModalProps = {
  title: string;
  children: ReactNode;
  canConfirmDelete?: boolean;
  onConfirm: () => void;
  onCancel: () => void;
};

const DeleteModal = ({children, title, canConfirmDelete = true, onConfirm, onCancel}: DeleteModalProps) => {
  const translate = useTranslate();

  return (
    <Modal closeTitle={translate('pim_common.close')} onClose={onCancel} illustration={<DeleteIllustration />}>
      <Modal.SectionTitle color="brand">{title}</Modal.SectionTitle>
      <Modal.Title>{translate('pim_common.confirm_deletion')}</Modal.Title>
      {children}
      <Modal.BottomButtons>
        <Button level="tertiary" onClick={onCancel}>
          {translate('pim_common.cancel')}
        </Button>
        <Button level="danger" disabled={!canConfirmDelete} onClick={onConfirm}>
          {translate('pim_common.delete')}
        </Button>
      </Modal.BottomButtons>
    </Modal>
  );
};

export {DeleteModal};
