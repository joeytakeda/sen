<?php

namespace AppBundle\Tests\Services;

use AppBundle\DataFixtures\ORM\LoadCity;
use AppBundle\DataFixtures\ORM\LoadEvent;
use AppBundle\DataFixtures\ORM\LoadEventCategory;
use AppBundle\DataFixtures\ORM\LoadLedger;
use AppBundle\DataFixtures\ORM\LoadLocation;
use AppBundle\DataFixtures\ORM\LoadLocationCategory;
use AppBundle\DataFixtures\ORM\LoadNotary;
use AppBundle\DataFixtures\ORM\LoadPerson;
use AppBundle\DataFixtures\ORM\LoadRace;
use AppBundle\DataFixtures\ORM\LoadRelationshipCategory;
use AppBundle\DataFixtures\ORM\LoadTransactionCategory;
use AppBundle\Entity\City;
use AppBundle\Entity\Ledger;
use AppBundle\Entity\Location;
use AppBundle\Entity\LocationCategory;
use AppBundle\Entity\Notary;
use AppBundle\Entity\Person;
use AppBundle\Entity\Race;
use AppBundle\Entity\RelationshipCategory;
use AppBundle\Entity\Transaction;
use AppBundle\Entity\TransactionCategory;
use AppBundle\Services\ImportService;
use Nines\UtilBundle\Tests\Util\BaseTestCase;
use const MB_CASE_UPPER;
use function mb_convert_case;

class ImportServiceTest extends BaseTestCase {

    /**
     * @var ImportService
     */
    private $importer;

    protected function setUp() {
        parent::setUp();
        $this->importer = $this->container->get(ImportService::class);
    }

    protected function getFixtures() {
        return array(
            LoadNotary::class,
            LoadLedger::class,
            LoadRace::class,
            LoadPerson::class,
            LoadCity::class,
            LoadRelationshipCategory::class,
            LoadTransactionCategory::class,
            LoadLocationCategory::class,
            LoadLocation::class,
            LoadEventCategory::class,
            LoadEvent::class,
        );
    }

    public function testConfig() {
        $this->assertInstanceOf(ImportService::class, $this->importer);
    }

    public function testFindNotary() {
        $notary = $this->importer->findNotary('Billy Terwilliger');
        $this->assertInstanceOf(Notary::class, $notary);
        $this->assertNotNull($notary->getId());
        $this->assertEquals('Billy Terwilliger', $notary->getName());
    }

    public function testNewFindNotary() {
        $notary = $this->importer->findNotary('Bobby Terwilliger');
        $this->assertInstanceOf(Notary::class, $notary);
        $this->assertNull($notary->getId());
        $this->assertEquals('Bobby Terwilliger', $notary->getName());
    }

    public function testFindLedger() {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, "9; 10", "1794");
        $this->assertInstanceOf(Ledger::class, $ledger);
        $this->assertNotNull($ledger->getId());
        $this->assertEquals(1794, $ledger->getYear());
    }

    public function testNewFindLedger() {
        $notary = $this->getReference('notary.1');
        $ledger = $this->importer->findLedger($notary, "11; 12", "1795");
        $this->assertInstanceOf(Ledger::class, $ledger);
        $this->assertNull($ledger->getId());
        $this->assertEquals(1795, $ledger->getYear());
    }

    public function testFindRace() {
        $race = $this->importer->findRace('indian');
        $this->assertInstanceOf(Race::class, $race);
        $this->assertNotNull($race->getId());
        $this->assertEquals('indian', $race->getName());
    }

    public function testNewFindRace() {
        $race = $this->importer->findRace('non-indian');
        $this->assertInstanceOf(Race::class, $race);
        $this->assertNull($race->getId());
        $this->assertEquals('non-indian', $race->getName());
    }

    /**
     * @dataProvider findPersonData
     */
    public function testFindPerson($given, $family, $raceName, $status) {
        $found = $this->importer->findPerson($given, $family, $raceName, $status);
        $this->assertInstanceOf(Person::class, $found);
        $this->assertNotNull($found->getId());
        $this->assertEquals(mb_convert_case($family, MB_CASE_UPPER), $found->getLastName());
    }

    public function findPersonData() {
        return [
            ['Emery', 'Ville', 'indian', 'free'],
            ['Emery', 'Ville', 'non-indian', 'free'],
            ['Emery', 'Ville', null, 'free'],
            ['Emery', 'Ville', 'indian', 'nonfree'],
            ['Emery', 'Ville', null, null],
            ['Emery', 'Ville', 'non-indian', 'non-free'],
        ];
    }

    /**
     * @dataProvider findNewPersonData
     */
    public function testFindNewPerson($given, $family, $raceName, $status) {
        $found = $this->importer->findPerson($given, $family, $raceName, $status);
        $this->assertInstanceOf(Person::class, $found);
        $this->assertNull($found->getId());
        $this->assertEquals(mb_convert_case($family, MB_CASE_UPPER), $found->getLastName());
    }

    public function findNewPersonData() {
        return [
            ['Coup', 'McCity', 'indian', 'free'],
            ['Coup', 'McCity', 'non-indian', 'free'],
            ['Coup', 'McCity', null, 'free'],
            ['Coup', 'McCity', 'indian', 'nonfree'],
            ['Coup', 'McCity', null, null],
            ['Coup', 'McCity', 'non-indian', 'non-free'],
            // Unicode
            ['Coup', 'Cóup', null, null],
            ['Coup', 'чащах', null, null],
            ['Coup', 'şoföre', null, null],
        ];
    }

    public function testFindCity() {
        $cify = $this->importer->findCity('Abbeville');
        $this->assertInstanceOf(City::class, $cify);
        $this->assertNotNull($cify->getId());
    }

    public function testNewFindCity() {
        $city = $this->importer->findCity('Ogdenville');
        $this->assertInstanceOf(City::class, $city);
        $this->assertNull($city->getId());
    }

    public function testFindRelationshipCategory() {
        $category = $this->importer->findRelationshipCategory('rel');
        $this->assertInstanceOf(RelationshipCategory::class, $category);
        $this->assertNotNull($category->getId());
    }

    public function testNewFindRelationshipCategory() {
        $category = $this->importer->findRelationshipCategory('accomplice');
        $this->assertInstanceOf(RelationshipCategory::class, $category);
        $this->assertNull($category->getId());
    }

    public function testFindTransactionCategory() {
        $category = $this->importer->findTransactionCategory('Sale of property');
        $this->assertInstanceOf(TransactionCategory::class, $category);
        $this->assertNotNull($category->getId());
    }

    public function testNewFindTransactionCategory() {
        $category = $this->importer->findTransactionCategory('Sale of stuff');
        $this->assertInstanceOf(TransactionCategory::class, $category);
        $this->assertNull($category->getId());
    }

    public function testNewFindRelationshipCategoryChars() {
        $category = $this->importer->findTransactionCategory('Jerk Nut');
        $this->assertInstanceOf(TransactionCategory::class, $category);
        $this->assertEquals('jerk-nut', $category->getName());
    }

    public function testCreateTransaction() {
        $ledger = $this->getReference('ledger.1');
        $first = $this->getReference('person.1');
        $second = $this->getReference('person.2');
        $row = [
            8 => null,
            9 => 'and wife',
            10 => 'from',
            15 => null,
            16 => 'and husband',
            17 => 'sale of property',
            18 => '01-03',
            3 => 1790,
            19 => 3,
            20 => 'Test transaction',
        ];

        $transaction = $this->importer->createTransaction($ledger, $first, $second, $row);
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    /**
     * @dataProvider parseDateData
     */
    public function testParseDate($expected, $data) {
        $result = $this->importer->parseDate($data);
        $this->assertEquals($expected, $result);
    }

    public function parseDateData() {
        return [
            [null, null],
            [null, false],
            [null, ''],
            [null, 0],
            ['1790-03-01', '01 Mar 1790'],
            ['1790-03-01', '01  mar   1790'],
            ['1790-03-01', '01mar1790'],
            ['1790-03-01', '01 MAR 1790'],
            ['1790-03-01', '01 mar 1790'],
            ['1790-02-01', '1 Feb 1790'],
            ['1790-02-00', 'Feb 1790'],
            ['1790-02-00', '     Feb    1790 '],
            ['1790-02-00', 'FEB 1790'],
            ['1790-02-00', 'feb 1790'],
            ['1790-00-00', '1790'],
            ['1790-00-00', '  1790  '],
            ['1790-03-01', 'ca 01 Mar 1790'],
            ['1790-03-01', 'abt 01  mar   1790'],
            ['1790-03-01', 'bef 01mar1790'],
            ['1790-03-01', 'abt 01 MAR 1790'],
            ['1790-03-01', 'aft 01 mar 1790'],
            ['1777-00-00', 'bef 1777?'],
            ['1749-00-00', '1749?'],
            ['1767-12-30', '30 Dec 1767?'],
            ['1818-06-00', '[13?] Jun 1818'],
        ];
    }

    public function testFindLocationCategory() {
        $category = $this->importer->findLocationCategory('church');
        $this->assertInstanceOf(LocationCategory::class, $category);
        $this->assertNotNull($category->getId());
    }

    public function testNewFindLocationCategory() {
        $category = $this->importer->findLocationCategory('chancery');
        $this->assertInstanceOf(LocationCategory::class, $category);
        $this->assertNull($category->getId());
    }

    public function testFindLocation() {
        $location = $this->importer->findLocation('Saint Barnabas Church', 'church');
        $this->assertInstanceOf(Location::class, $location);
        $this->assertNotNull($location->getId());
    }

    public function testNewFindLocation() {
        $location = $this->importer->findLocation('Saint Johns Church', 'church');
        $this->assertInstanceOf(Location::class, $location);
        $this->assertNull($location->getId());
    }

    public function testAddNullManumission() {
        $person = $this->getReference('person.1');
        $event = $this->importer->addManumission($person, []);
        $this->assertNull($event);
    }

    public function testAddManumission() {
        $person = $this->getReference('person.1');
        $row = [
            7 => '5 Jun 1771',
        ];
        $event = $this->importer->addManumission($person, $row);
        $this->assertNotNull($event);
        $this->assertEquals('1771-06-05', $event->getDate());
        $this->assertEquals('5 Jun 1771', $event->getWrittenDate());
    }

    public function testAddNullBaptism() {
        $person = $this->getReference('person.1');
        $event = $this->importer->addBaptism($person, []);
        $this->assertNull($event);
    }

    public function testAddBaptism() {
        $person = $this->getReference('person.1');
        $row = [
            5 => '5 Jun 1771',
        ];
        $event = $this->importer->addBaptism($person, $row);
        $this->assertNotNull($event);
        $this->assertEquals('1771-06-05', $event->getDate());
        $this->assertEquals('5 Jun 1771', $event->getWrittenDate());
    }

    public function testAddNullResidence() {
        $person = $this->getReference('person.1');
        $residences = $person->getResidences()->count();
        $this->importer->addResidence($person, []);
        $this->assertEquals($residences, $person->getResidences()->count());
    }

    public function testAddResidence() {
        $person = $this->getReference('person.1');
        $residences = $person->getResidences()->count();
        $row = [
            9 => '5 Jun 1771',
            10 => 'Chicago',
        ];
        $this->importer->addResidence($person, $row);
        $this->assertEquals($residences+1, $person->getResidences()->count());
    }

    public function testAddNullAliases() {
        $person = $this->getReference('person.1');
        $aliases = count($person->getAlias());
        $this->importer->addAliases($person, []);
        $this->assertEquals($aliases, count($person->getAlias()));
    }

    public function testAddAliases() {
        $person = $this->getReference('person.1');
        $aliases = count($person->getAlias());
        $row = [
            11 => 'Driver, cheese maker',
        ];
        $this->importer->addAliases($person, $row);
        $this->assertEquals($aliases+2, count($person->getAlias()));
    }

    public function testSetNullNative() {
        $person = $this->getReference('person.1');
        $native = $person->getNative();
        $this->importer->setNative($person, []);
        $this->assertEquals($native, $person->getNative());
    }

    public function testSetNative() {
        $person = $this->getReference('person.1');
        $row = [
            12 => 'Chicago',
        ];
        $this->importer->setNative($person, $row);
        $this->assertEquals('Chicago', $person->getNative());
    }

    public function testAddNullOccupations() {
        $person = $this->getReference('person.1');
        $occupations = count($person->getOccupation());
        $this->importer->addOccupations($person, []);
        $this->assertEquals($occupations, count($person->getOccupation()));
    }

    public function testAddOccupations() {
        $person = $this->getReference('person.1');
        $occupations = count($person->getOccupation());
        $row = [
            13 => 'Driver; cheese maker',
        ];
        $this->importer->addOccupations($person, $row);
        $this->assertEquals($occupations+2, count($person->getOccupation()));
    }

}