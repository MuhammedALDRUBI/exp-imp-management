# Exporter : 
##### It is an abstract class that can be extended to define new exporter types .
##### The algorithm :
All Exporter types globally try to stream the exported data file 
to the end user (( as long as this data is in small size ... once it is in large size the exported data file asset downloading link will be sent to the logged user by email )) .
##### All exporter classes are serlizables ... so feel free to pass them any job or queue object.

## Exporter Global methods :
##### __construct(?string $modelClass = null)  
pass it a model class to fetch data from database before exporting it , or you can pass the model class later by setModelClass method , or even set a data collection manually to export it.
##### setModelClass(string $modelClass): self
This method is just to pass a model class after object construction is done ... pass it a model class to fetch data from database before exporting it .
##### setSpatieBuilderAllowedFilters(array $allowedFilters) : self
by defalt Exporter uses Spatie's QueryBuilder ... you can pass the allowedFilters by this method .
##### useQueryBuilder(Builder | DatabaseQueryBuilder | QueryBuilder $builder) : self
by default the Exporter class uses Spatie 's QueryBuilder class ... you can change it by using this method .
Note : even you don't want to change the builder type ... you need to change builder if you want to disable the default applied scopes or adding some extra query methods on builder .... otherwiese you will need to extend the Exporter chuild class to customize
query bulding operation .
#####  useDataCollection( Collection | LazyCollection|null $DataCollection = null ) : self
pass it a data collection to export it ... if this method is used there is no model class will be required and there is no data will be fetched from database .
##### export(string $documentTitle) : JsonResponse | StreamedResponse
pass it the title will be used in naming the exported file , by default the package will add the date to the name to make its name unique , 
if you want to use the same title you passed you can use useTheSameDocumentTitle method.
##### useTheSameDocumentTitle(): self
is used to instruct the Exporter class to use the same file name will be passed in export method (use it before export method calling).

## Exporter main types : 
- PDFExporter
- CSVExporter
These type are defined in the package and you can use them directly or extending them to have more control on functionallity .

### PDFExporter : 
Is used to export pdf files , The package by defaul require a PixelDomPdf\Interfaces\PixelPdfNeedsProvider typed object to be bind
into laravel service container and by default the package uses pixel-dom-pdf to achieve that

#### PDFExporter Extra methods :
##### setViewTemplateRelativePath(string $viewTemplateRelativePath) : self
is used to define the view path that will be rendered with the exported data to get the final html will be used in the pdf .
note : this method must be called before export method ... and it is required because the exporting operation will faill without setting view path .
##### getViewTemplateRelativePath() : ?string
To getting view path that currently set on the object .
 
 Ex 1 : Exporting Departments as a pdf  
(new PDFExporter(Department::class))->setViewTemplateRelativePath("welcome")->export("testDepPdf")


Ex2 : Exporting Departments collection as a pdf  

$collection = Department::get();
(new PDFExporter())->useDataCollection($collection)->setViewTemplateRelativePath("welcome")->export("testDepPdf")

### CSVExporter
Is used to export a csv file has the exported data , The package by defaul require a ExpImpManagement\Interfaces\PixelExcelExpImpLib
typed object to be bind into laravel service container and by default the package uses Rap2hpoutre\FastExcel\FastExcel package to achieve that.

Ex : Exporting Departments collection as a csv  
(new CSVExporter(Department::class))->useDataCollection($collection)->export("testDepExporting")

#### CSVExporter Extra methods : There is no extra methods ... Exporter type mathods are available.

<hr>

## Exporters extending :
By extending the exporter type you will be able to control every thing in exporting cycle , you will be able to cusomize query building operation .
### Tips :
1.1 - Any Exporter child class that implements ExpImpManagement\ExportersManagement\Interfaces\SelfConstructableExporter interface 
and uses ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterSelfConstructing trait doesn't need to pass model class in the __construct method while calling it from the controller context .... The ExporterSelfConstructing trait will declare the __construct and pass it the model class defined by 
SelfConstructableExporter interface 's getModelClassForSelfConstructing method .
1.2 - If the class implements ExpImpManagement\ExportersManagement\Interfaces\SelfConstructableExporter interface and need to declare  the __construct  to pass some other props ... Don't use ExpImpManagement\ExportersManagement\Exporter\Traits\ExporterSelfConstructing trait and declare  the __construct  method as you like ( but don't forget to pass the modelClass to parent::__construct method ) .
2.1 - By default our package doesn't handle relationships ... but you can extend the exporter type and add the relationships you want (( If the result exporter class has the functinality to handle them , ex : PDFExporter pass the data to the view and render it by PixelDomPdf ... so you can load relationships and use them in your view before rendering it )) .
2.2 By default each exporter has an empty array of allowedFilters .... so no allowedFitlers will be set until you set them .
3.1 you can override any thing by extending class ... ex : exported file naming methods , Responders classes , notifications classes , overriding the serlizied props .... etc. .

<hr>

# ExportingBaseService :
It is an abstract class ... used to check request "type" query string value ... and pass it to  abstract method :
protected function initExporter($exporterType) : Exporter
which must be defined by the child class to initialize the convenient exporter type based on $exporterType value .

## methods :
### public function basicExport(string $documentTitle) : JsonResponse | StreamedResponse
used to apply the export directly on the initialized exporter
### public function callOnExporter(callable $callback)
is used to access and call a callback on the exporter to set it some props or use any of its public methods before exporting data 