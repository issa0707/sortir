<?php


namespace App\Outils;


class RechercheVillesClass
{
    /**
     * @var
     */
    private ?string $contient=null;

    /**
     * @return String
     */
    public function getContient(): ?string
    {
        return $this->contient;
    }

    /**
     * @param String $contient
     * @return RechercheVillesClass
     */
    public function setContient(?string $contient): RechercheVillesClass
    {
        $this->contient = $contient;
        return $this;
    }

    //getter et setter



}