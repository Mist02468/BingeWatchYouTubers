namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CityTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCity($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    
    public function getCityByName($name)
    {
        $name = (string) $name;
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        return $row;
    }

    public function saveCity(City $city)
    {
        $data = array(
            'name' => $city->name
        );

        $id = (int) $city->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $city->id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCity($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('City id does not exist');
            }
        }
        return $city;
    }

    public function deleteCity($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}