namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class StateTable
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

    public function getState($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }
    
    public function getStateByName($name)
    {
        $name = (string) $name;
        $rowset = $this->tableGateway->select(array('name' => $name));
        $row = $rowset->current();
        return $row;
    }

    public function saveState(State $state)
    {
        $data = array(
            'name' => $state->name
        );

        $id = (int) $state->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $state->id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getState($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('State id does not exist');
            }
        }
        return $state;
    }

    public function deleteState($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}