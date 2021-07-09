<?php


namespace App\Outils;


use App\Entity\Campus;


class RechercheSortieClass
{
    /**
     * @var Campus
     */
    private Campus $campus;
    /**
     * @var
     */
    private  $contient;
    /**
     * @var
     */
    private  $apres;
    /**
     * @var
     */
    private  $avant;
    /**
     * @var
     */
    private  $organise;
    /**
     * @var
     */
    private  $inscrit;
    /**
     * @var
     */
    private $pasInscrit;
    /**
     * @var
     */
    private $passees;
    /**
     * @var
     */
    private $user;





    //getter et setter




    /**
     * @return Campus
     */
    public function getCampus(): Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     * @return RechercheSortieClass
     */
    public function setCampus(Campus $campus): RechercheSortieClass
    {
        $this->campus = $campus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContient()
    {
        return $this->contient;
    }

    /**
     * @param mixed $contient
     * @return RechercheSortieClass
     */
    public function setContient($contient)
    {
        $this->contient = $contient;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApres()
    {
        return $this->apres;
    }

    /**
     * @param mixed $apres
     * @return RechercheSortieClass
     */
    public function setApres($apres)
    {
        $this->apres = $apres;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvant()
    {
        return $this->avant;
    }

    /**
     * @param mixed $avant
     * @return RechercheSortieClass
     */
    public function setAvant($avant)
    {
        $this->avant = $avant;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrganise()
    {
        return $this->organise;
    }

    /**
     * @param mixed $organise
     * @return RechercheSortieClass
     */
    public function setOrganise($organise)
    {
        $this->organise = $organise;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     * @return RechercheSortieClass
     */
    public function setInscrit($inscrit)
    {
        $this->inscrit = $inscrit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPasInscrit()
    {
        return $this->pasInscrit;
    }

    /**
     * @param mixed $pasInscrit
     * @return RechercheSortieClass
     */
    public function setPasInscrit($pasInscrit)
    {
        $this->pasInscrit = $pasInscrit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassees()
    {
        return $this->passees;
    }

    /**
     * @param mixed $passees
     * @return RechercheSortieClass
     */
    public function setPassees($passees)
    {
        $this->passees = $passees;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return RechercheSortieClass
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }





}





