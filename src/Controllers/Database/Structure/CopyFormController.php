<?php

declare(strict_types=1);

namespace PhpMyAdmin\Controllers\Database\Structure;

use PhpMyAdmin\Controllers\InvocableController;
use PhpMyAdmin\Current;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Http\Response;
use PhpMyAdmin\Http\ServerRequest;
use PhpMyAdmin\ResponseRenderer;

use function __;

final class CopyFormController implements InvocableController
{
    public function __construct(private readonly ResponseRenderer $response)
    {
    }

    public function __invoke(ServerRequest $request): Response|null
    {
        /** @var string[] $selected */
        $selected = $request->getParsedBodyParam('selected_tbl', []);

        if ($selected === []) {
            $this->response->setRequestStatus(false);
            $this->response->addJSON('message', __('No table selected.'));

            return null;
        }

        $urlParams = ['db' => Current::$database];
        foreach ($selected as $selectedValue) {
            $urlParams['selected'][] = $selectedValue;
        }

        $databasesList = DatabaseInterface::getInstance()->getDatabaseList();
        foreach ($databasesList as $key => $databaseName) {
            if ($databaseName == Current::$database) {
                $databasesList->offsetUnset($key);
                break;
            }
        }

        $this->response->disable();
        $this->response->render('database/structure/copy_form', [
            'url_params' => $urlParams,
            'options' => $databasesList->getList(),
        ]);

        return null;
    }
}
