<?php

declare(strict_types=1);

namespace App\Modules\Errors;

use Phalcon\Mvc\Controller as BaseController;

class Controller extends BaseController
{
    public function notFoundAction()
    {
        if ($this->expectJson()) {
            return $this->response->setStatusCode(404)->setJsonContent([
                'code'    => 1404,
                'message' => 'Not found',
            ]);
        }

        $redirectTo = $this->session->has('user')
            ? '/Errors/notFoundDashboard'
            : '/Errors/Dashboard';

        $this->view->setMainView($redirectTo);
    }

    public function forbiddenAction($context = null, $exception = null)
    {
        if ($this->expectJson()) {
            switch ($context) {
                case 'formValidation':
                    $errorMessages = $exception->getErrorMessages();
                    $errors = [];
                    foreach ($errorMessages as $message)
                        if (!isset($errors[$message->getField()]))
                            $errors[$message->getField()] = $message->getMessage();

                    return $this->response->setStatusCode(403)->setJsonContent([
                        'code'    => 1403,
                        'message' => $errorMessages[0]->getMessage(),
                        'errors'  => $errors,
                    ]);
                    break;

                case 'dbaError':
                    [$sqlCode, $errCode, $message] = $exception->errorInfo;

                    return $this->response->setStatusCode(403)->setJsonContent([
                        'code'    => 1403,
                        'message' => $message,
                    ]);
                    break;
            }
        }
    }

    public function unauthorizedAction($type = null, $requiredRoles = null)
    {
        if ($this->expectJson()) {
            return $this->response->setStatusCode(401)->setJsonContent([
                'code'    => 1401,
                'message' => 'Unauthorized',
            ]);
        }

        if (!$this->session->has('user')) {
            /** @var array */
            $queries = $this->request->getQuery();
            if (!isset($queries['_url'])) {
                return $this->response->redirect('/panel/auth/login');
            } else {
                $intended = $queries['_url'];
                unset($queries['_url']);

                $intended .= empty($queries) ? '' : ('?' . http_build_query($queries));

                $this->session->set('intended', $intended);

                return $this->response->redirect('/panel/auth/login');
            }
        }

        if ($type != 'requireRole') {
            return $this->response->redirect('/panel/dashboard');
        }

        $this->response->setStatusCode(401);
    }

    private function expectJson(): bool
    {
        return $this->request->isAjax() || $this->request->getBestAccept() == 'application/json';
    }
}
