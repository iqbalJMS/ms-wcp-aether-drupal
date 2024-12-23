<?php

namespace Drupal\brimw\Normalizer;

class SimulationNormalizer extends BaseParagraphNormalizer 
{
  /**
   * Array of supported paragraph types.
   *
   * @var array
   */
  protected $supportedParagraphType = 'simulation_item';

  /**
   * @inheritDoc
   */
  public function normalize(
    $entity,
    $format = NULL,
    array $context = []
  ): array {
    $normalized = parent::normalize(
      $entity,
      $format,
      $context
    );

    $normalized['config'] = \Drupal::service('brimw.simulation_remote_data')
                                    ->getMasterData(
                                      $this->convertInstallmentSchemeIntoMasterDataKey($entity->field_simulation->value)
                                    );

    return $normalized;
  }

  protected function convertInstallmentSchemeIntoMasterDataKey($key)
  {
    return [
      "KPR" => "kpr",
      "KPRS" => "kprs",
      "BRITAMA_RENCANA" => "britamaRencana",
      "BRIGUNA_UMUM" => "brigunaUmum",
      "BRIGUNA_KARYA" => "brigunaKarya",
      "BRIGUNA_PURNA" => "brigunaPurna",
      "DEPOSITO" => "deposito",
      "DEPOSITO_VALAS" => "depositoValas",
      "DEPOSITO_BISNIS" => "depositoBisnis",
      "DEPOSITO_BISNIS_VALAS" => "depositoBisnisValas",
      "INVESTASI_DPLK" => "investasiDplk",
      "KREDIT_INVESTASI" => "kreditInvestasi",
      "CICILAN_KENDARAAN" => "vehicleInstallment",
    ][$key] ?? 'all';
  }

}
