<?php
namespace App\Entity;

class UsersSearch
{
    // /**
    //  * @var string|null
    //  */
    // private $activite;
    /**
     * @var int|null
     */
    private $age;
    /**
     * @var string|null
     */
    private $sexe;
    // /**
    //  * @var string|null
    //  */
    // private $ville;

    // /**
    //  * Get the value of activite
    //  *
    //  * @return  string|null
    //  */
    // public function getActivite()
    // {
    //     return $this->activite;
    // }

    // /**
    //  * Set the value of activite
    //  *
    //  * @param  string|null  $activite
    //  *
    //  * @return  self
    //  */
    // public function setActivite($activite)
    // {
    //     $this->activite = $activite;

    //     return $this;
    // }

    /**
     * Get the value of age
     *
     * @return  int|null
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set the value of age
     *
     * @param  int|null  $age
     *
     * @return  self
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get the value of sexe
     *
     * @return  string|null
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set the value of sexe
     *
     * @param  string|null  $sexe
     *
     * @return  self
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    // /**
    //  * Get the value of ville
    //  *
    //  * @return  string|null
    //  */
    // public function getVille()
    // {
    //     return $this->ville;
    // }

    // /**
    //  * Set the value of ville
    //  *
    //  * @param  string|null  $ville
    //  *
    //  * @return  self
    //  */
    // public function setVille($ville)
    // {
    //     $this->ville = $ville;

    //     return $this;
    // }
}
