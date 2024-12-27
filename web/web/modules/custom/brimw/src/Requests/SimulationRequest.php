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
        'estimateDepositoBusinessValas',
        'estimateInvestment',
        'estimateInitialInvestment',
        'estimateVehicleInstallment',
        'estimateObligasi',
        'estimateReksadana',
        'estimateKreditInvestasi',
    ];
      
    public function validateType(string $simulation)
    {
        return in_array($simulation, $this->availableTypes);
    }

    public function validate(): void
    {
        $simulation = $this->request->get('simulation');
        if (!$this->validateType($simulation)) {
            $this->finalizeValidation(['simulation' => 'Invalid simulation type']);
        }

        $validateMethod = 'validate'.ucfirst($simulation);
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
                'durationInMonths',
                new NotBlank, 
            ), 
            $this->rules(
                'monthlyDeposit',
                new NotBlank, 
            ),
            $this->rules(
                'insurancePremium',
                new NotBlank,
            ),
        ));
    }
    
    protected function validateEstimateBriguna(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules('salary', new NotBlank),
            $this->rules('installmentTerm', new NotBlank),
            $this->rules('interestRate', new NotBlank),
            $this->rules('type', new NotBlank),
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
            $this->rules(
                'currency',
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
    
    protected function validateEstimateDepositoBusinessValas(array $errors = [])
    {
        return $this->finalizeValidation(array_merge(
            $this->rules(
                'interestRate',
                new NotBlank, 
            ),
            $this->rules(
                'depositAmount',
                new NotBlank, 
            ), 
            $this->rules(
                'termInMonths',
                new NotBlank, 
            ),
            $this->rules(
                'currency',
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
    
    protected function validateEstimateKreditInvestasi(array $errors = [])
    {
        return $this->finalizeValidation(array_merge( 
            $this->rules(
                'installmentTerm',
                new NotBlank, 
            ),
            $this->rules(
                'installment',
                new NotBlank, 
            ),
            $this->rules(
                'InterestRate',
                new NotBlank, 
            ),
        ));
    }
}