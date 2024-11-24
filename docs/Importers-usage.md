# Importer : 
##### It is an abstract class that can be extended to define new importer types .
##### The algorithm :
Each importer expects to receive an UploadedFile from the request ... It will move the file to a temporary path to process it in the background by job ,
after file is moved successfully the end logged user will see the message : 
"Your Data File Has Been Uploaded Successfully ! , You Will Receive Your Request Result By Mail Message On Your Email !"
then will receive the result of importing into his email and in the runtime .

Any row insertions fails because of validation or of any other reason will be ignored , and failing processing will be the child classes mission (in CSVImporter there are some processing will be done ) .
Any Importer child class will be able to handle every thing in the cycle , but also you don't need to extend the importer class every time you want to use ,
you can init it by __construct method normally .
 
##### All importers classes are serlizables ... so feel free to pass them any job or queue object.

## importers Global methods :
#####  __construct(string $ModelClass , string $dataValidationRequestFormClass ) 
pass the model class will be used in importing data and then pass the validation request form class that will be used in data validation .
###### Note : because of the ignoring of any row insertion has failed , there is no gernerally validation that was using rules() method found in requesty form class , the validation in this case will only be applied on  getModelKeyAdvancedValidationRules(array $data = []) method defined in class implements CRUDServices\Interfaces\ValidationManagerInterfaces\NeedsModelKeyAdvancedValidation interface .

#####  setDataValidationRequestFormClass(string $requestFormClass)  : self
to change Data Validaiton request form class after object construction is done ... must be called before import method calling .
##### import() : JsonResponse 
starts to implement importing algorithm that we talked about above .

## Importers main types : 
- CSVImporter
This type is defined in the package and you can use them directly or extending them to have more control on functionallity .

### CSVImporter : 
this importer type has extended the importing algorithm :
1 - it require you to pass a CSVImportableFileFormatFactory instance to use it in :
- streaming csv importable file format to be fillable by user in strict data validation conditions .
- handling the rejected data in a csv importable format to be manually changed by user in the same data validation conditions (this point needs to discuss with team because the rejected data maybe rejected because of validation so it can't be filled into the format file).
2 - it handles the failling on validation , failing in data insertion and the rejected data rows are filled in a format file to be sent to the end user by email as a result of the importing process .
3- for reading file data the package by defaul require a ExpImpManagement\Interfaces\PixelExcelExpImpLib
typed object to be bind into laravel service container and by default the package uses Rap2hpoutre\FastExcel\FastExcel package to achieve that.
4- for format faking the package by defaul require a ExpImpManagement\Interfaces\PixelExcelFormatFactoryLib typed object to be bind into laravel service container and by default the package uses Laravel excel (Maatwebsite/excel) package to achieve that.

#### CSVImporter Extra methods :
##### __construct(string $ModelClass , string $dataValidationRequestFormClass , CSVImportableFileFormatFactory $templateFactory)
##### public function setImportableFileFormatFactory(CSVImportableFileFormatFactory $templateFactory) : self
Setter for CSVImportableFileFormatFactory after construction is done
##### public function getImportableFileFormatFactory() : CSVImportableFileFormatFactory
Getter for CSVImportableFileFormatFactory 
##### public function downloadFormat()
is used to streaming the file format to be filled by te user to be usable in imporitng process , it represent the rules must the data follow to be inserted correctly .
 
 Ex 1 : Downloading Departments importable format  
 $templateFac =  new DepartmentsCSVImportableFileFormatFactory( "department_improting_format"  );
(new CSVImporter(Department::class , DepartmentStoringRequest::class , $templateFac))->downloadFormat();


Ex2 : Importing Departments from file uploaded from the request  
$templateFac =  new DepartmentsCSVImportableFileFormatFactory( "department_improting_format"  );
(new CSVImporter(Department::class , DepartmentStoringRequest::class , $templateFac))->import();
  
<hr>

### Importers extending :
By extending the importer type you will be able to control every thing in exporting cycle , you will be able to cusomize query building operation , handling data insertion failing , managing the behaviors on each point of the importing cycle.
#### Tips :
1.1 - Any CSVImporter child class that implements ExpImpManagement\ImportersManagement\Interfaces\SelfConstructableCSVImporter interface 
and uses ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\CSVImporterSelfConstructing trait doesn't need to pass anything into the __construct method while calling it from the controller context .... The CSVImporterSelfConstructing trait will declare the __construct and pass it the arguments defined by SelfConstructableCSVImporter interface 's getModelClassForSelfConstructing , getDataValidationRequestFormClassForSelfConstructing , getImportableTemplateFactoryForSelfConstructing  methods .
1.2 - If the class implements ExpImpManagement\ImportersManagement\Interfaces\SelfConstructableCSVImporter interface and need to declare  the __construct  to pass some other props ... Don't use ExpImpManagement\ImportersManagement\ImporterTypes\CSVImporter\Traits\CSVImporterSelfConstructing trait and declare  the __construct  method as you like ( but don't forget to pass the modelClass , dataValidationREquestFormClass , ImportableDataFileFactory arguments to parent::__construct method ) .
2 - By default our package doesn't handle relationships ... but you can extend the exporter type and add the relationships you want (( If the result importer class has the functinality to handle them , ex : you can create a new type that handle other files uploaded to the request and insert them as you want with a new functionallity you write 
3 you can override any thing by extending class ... ex : uploaded file handling methods , storign in the temp path or change the path , filesProcessors , Responders classes , notifications classes , overriding the serlizied props .... etc. .

<hr>

### creating a CSVImportableFileFormatFactory :
to get a new CSVImportableFileFormatFactory type you need to create a class extends 
ExpImpManagement\ImportersManagement\ImportableFileFormatFactories\CSVImportableFileFormatFactory\CSVImportableFileFormatFactory
and define the abstract method :
protected function getColumnFormatInfoCompoenents() : array;
this method must return an array of CSVFormatColumnInfoComponent objects .
then you need to pass the format file name into its constructor .

#### methods :
##### public function __construct(string $fileName, array $headers = [])
##### public function setHeaders(array $headers = []) : self
setting the headers used on streaming format file
##### public function setFileName(string $fileName) : self
setting the format file name after construction is done
##### public function downloadFormat()
download and stream a format file to be fillablke by the user to help it fill correct info .
##### public function getRawContent()
is used to get the format content as string , it used on CSVImporrter insertion failling handling cases to send the user a format file with the rejected data.
##### public function headings(): array
returns the headings of the format file
##### public function collection()
returns the data will represent the rows in format file .... normally it is used for the rejected dfata only and will not be used for an empty format file .

Note : By extending the class you can override any cycle point as you need .

<hr>

### creating CSVFormatColumnInfoComponent :
it is required for creating CSVImportableFileFormatFactory as we talked above .
### methods :
#### public function __construct(string $columnCharSymbol , string $columnHeaderName  )
#### public function getColumnCharSymbol(): string
Gets the column character used by the column in format file
#### public function setColumnCharSymbol(string $columnCharSymbol): self
Sets which column character must be used by the column in format file
#### public function getColumnHeaderName(): string
Gets column header name in format file
#### public function setColumnHeaderName(string $columnHeaderName): self
Sets column header name in format file
#### public function setCellDataValidation(CSVCellValidationDataTypeSetter $cellValidationSetter) : self
Sets CSVCellValidationDataTypeSetter for column in format file
#### public function getCellDataValidation() : ?CSVCellValidationDataTypeSetter
Gets CSVCellValidationDataTypeSetter defined for column in format file
#### public function setColumnWidth(int $width) : self
Sets width of column in format file
#### public function getWidth() : ?int
Gets width of column in format file

<hr>

### CSVCellValidationDataTypeSetter  :
All you should now about these types is how to creat them and the porpuse of them .

#### ListCellValidationSetter :
used to set a dropdown list at the cell to enable the user to user between its values , these values can be values you 
fetched from database or what ever .
##### creating it :
###### public function __construct(array $options)
Then you can set it normally into CSVFormatColumnInfoComponent setCellDataValidation() method .  

#### DecimalCellValidationSetter :
used to set decimal validation rule on the cell .
##### creating it : by __construct method without required arguments .

#### TextLengthCellValidationSetter :
is used to set a specific text length rule on a column cell .
ex : use it for name has length must be in range 0-255
#### creating it :
public function __construct(int $maxLength , int $minLength = 0)
where these arguments represent the range of the character length .


#### DateCellValidationSetter :
is used to set a specific Date range rule on a column cell 
#### cretingit :
public function __construct(string $startDate , string $endDate )
where these arguments represent the range of the date .

#### TimeCellValidationSetter :
is used to set a specific time range rule on a column cell 
##### creating it :
public function __construct(string $startTime , string $endTime )
where these arguments represent the range of the time .