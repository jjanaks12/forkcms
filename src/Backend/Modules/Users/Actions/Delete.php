<?php

namespace Backend\Modules\Users\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionDelete as BackendBaseActionDelete;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\User as BackendUser;
use Backend\Modules\Users\Engine\Model as BackendUsersModel;
use Backend\Modules\Users\Form\UserDeleteType;

/**
 * This is the delete-action, it will deactivate and mark the user as deleted
 */
class Delete extends BackendBaseActionDelete
{
    public function execute(): void
    {
        $deleteForm = $this->createForm(UserDeleteType::class);
        $deleteForm->handleRequest($this->getRequest());
        if (!$deleteForm->isSubmitted() || !$deleteForm->isValid()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=something-went-wrong');
        }
        $deleteFormData = $deleteForm->getData();

        // get parameters
        $this->id = (int) $deleteFormData['id'];

        // does the user exist
        if ($this->id === 0
            || !BackendUsersModel::exists($this->id)
            || BackendAuthentication::getUser()->getUserId() === $this->id
        ) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=non-existing');
        }

        parent::execute();

        // get data
        $user = new BackendUser($this->id);

        // God-users can't be deleted
        if ($user->isGod()) {
            $this->redirect(BackendModel::createURLForAction('Index') . '&error=cant-delete-god');
        }

        // delete item
        BackendUsersModel::delete($this->id);

        // item was deleted, so redirect
        $this->redirect(
            BackendModel::createURLForAction('Index') . '&report=deleted&var=' . $user->getSetting('nickname')
        );
    }
}
