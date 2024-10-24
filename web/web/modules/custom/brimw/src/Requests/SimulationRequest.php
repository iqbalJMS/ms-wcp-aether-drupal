<?php

namespace Drupal\brimw\Requests;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class SimulationRequest extends BaseRequest
{
    private $availableTypes = [
        'estimateKpr',
        'estimateKprs',
        'estimateBritamaRencana',
        'estimateBriguna',
        'estimateBrigunaKarya',
        'estimateBrigunaPurna',
        'estimateDeposito',
        'estimateDepositoValas',
        'estimateDepositoBusiness',
        'estimateInvestment',
        'estimateInitialInvestment',
        'estimateVehicleInstallment',
        'estimateObligasi',
        'estimateReksadana',
    ];
      
    public function validateType(string $type)
    {
        return in_array($type, $this->availableTypes);
    }

    public function validate(): void
    {
        $type = $this->request->get('type');
        if (!$this->validateType($type)) {
            $this->finalizeValidation(['type' => 'Invalid type']);
        }

        $validateMethod = 'validate'.ucfirst($type);
        if (method_exists($this, $validateMethod)){
            $this->{$validateMethod}();
        }
    }

    protected function validateEstimateKpr(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'installmentAmount',
                new NotBlank, 
            ),
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            )
        ));
    }

    protected function validateEstimateKprs(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'installmentAmount',
                new NotBlank, 
            ), 
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            )
        ));
    }

    protected function validateEstimateBritamaRencana(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'month',
                new NotBlank, 
            ), 
            $this->rules(
                'amount',
                new NotBlank, 
            ),
            $this->rules(
                'premiAsuransi',
                new NotBlank,
            ),
        ));
    }
    
    protected function validateEstimateBriguna(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules('karyaSalary', new NotBlank),
            $this->rules('karyaInstallmentTerm', new NotBlank),
            $this->rules('karyaInterestRate', new NotBlank),
            $this->rules('purnaSalary', new NotBlank),
            $this->rules('purnaInstallmentTerm', new NotBlank),
            $this->rules('purnaInterestRate', new NotBlank),
        ));

    }
    
    protected function validateEstimateBrigunaKarya(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'salary',
                new NotBlank, 
            ), 
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            ), 
            $this->rules(
                'interestRate',
                new NotBlank,
            ),
        ));

    }
    
    protected function validateEstimateBrigunaPurna()
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'salary',
                new NotBlank, 
            ),
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            ),
            $this->rules(
                'interestRate',
                new NotBlank,
            ),
        ));
    }
    
    protected function validateEstimateDeposito()
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateDepositoValas(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateDepositoBusiness(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'depositAmount',
                new NotBlank, 
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateInvestment(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'investmentAmount',
                new NotBlank, 
            ),
            $this->rules(
                'interestRate',
                new NotBlank,
            ),
            $this->rules(
                'duration',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateInitialInvestment(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'targetInvestmentValue',
                new NotBlank, 
            ),
            $this->rules(
                'duration',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateVehicleInstallment(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'vehiclePrice',
                new NotBlank, 
            ),
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            ),
            $this->rules(
                'vehicleStatus',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateObligasi(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'amount',
                new NotBlank, 
            ),
            $this->rules(
                'term',
                new NotBlank, 
            ),
            $this->rules(
                'couponRate',
                new NotBlank, 
            ),
        ));
    }
    
    protected function validateEstimateReksadana(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'amount',
                new NotBlank, 
            ),
            $this->rules(
                'investmentType',
                new NotBlank, 
            ),
        ));
    }
}