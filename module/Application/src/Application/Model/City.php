namespace Application\Model

class City
{
    public $id;
    public $name;
    public $state;
    public $walkScore;
    public $avgTemp;
    public $population;
    public $landAreaSquareMiles;
    public $coordinatesNorth;
    public $coordinatesWest;
    
    public function exchangeArray($data)
    {
        $this->id                  = (!empty($data['id']))                  ? $data['id'] : null;
        $this->name                = (!empty($data['name']))                ? $data['name'] : null;
        $this->state               = (!empty($data['state']))               ? $data['state'] : null;
        $this->walkScore           = (!empty($data['walkScore']))           ? $data['walkScore'] : null;
        $this->avgTemp             = (!empty($data['avgTemp']))             ? $data['avgTemp'] : null;
        $this->population          = (!empty($data['population']))          ? $data['population'] : null;
        $this->landAreaSquareMiles = (!empty($data['landAreaSquareMiles'])) ? $data['landAreaSquareMiles'] : null;
        $this->coordinatesNorth    = (!empty($data['coordinatesNorth']))    ? $data['coordinatesNorth'] : null;
        $this->coordinatesWest     = (!empty($data['coordinatesWest']))     ? $data['coordinatesWest'] : null;
    }
}