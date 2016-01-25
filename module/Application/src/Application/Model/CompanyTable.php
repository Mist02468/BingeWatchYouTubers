namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CompanyTable
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

    public function getCompany($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        return $row;
    }

    public function saveCompany(Company $company)
    {
        $data = array(
            'name' => $company->name
        );

        $id = (int) $company->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $company->id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getCompany($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Company id does not exist');
            }
        }
        return $company;
    }

    public function deleteCompany($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}