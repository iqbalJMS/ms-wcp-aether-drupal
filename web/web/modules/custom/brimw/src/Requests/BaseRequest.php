<?php

namespace Drupal\brimw\Requests;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

abstract class BaseRequest
{
    public Request $request;

    public array $input;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function validate(): void
    {
        throw new \Exception('Method validate must be defined.');
    }

    public function rules($attribute, Constraint ... $constraints)
    {
        try {
            Validation::createCallable(
                ... $constraints
            )($this->request->get($attribute));
        } catch (ValidationFailedException $e) {
            foreach ($e->getViolations() as $violation) {
                $errors[$attribute] = $violation->getMessage();
                break;
            }

            return $errors;
        }

        return [];
    }

    public function finalizeValidation(array $errors): void
    {
        if (!$errors) {
            return;
        }

        foreach ($errors as $key => $message) {
            $messages['errors'][] = [
                'property' => $key,
                'message' => $message,
            ];
        }

        $response = new JsonResponse($messages, 422);
        $response->send();

        exit;
    }
}