Типа в кубе сделано через инвенты, но хз зачем

Типа у них логика такая

beforeLoadEvent = new BeforeEntityLoadEvent(loadContext);
fireEvent(beforeLoadEvent)

if (beforeLoadEvent.loadPrevented()) {
      return null;
}

data = loadData(context);

afterLoadEvent = new AfterEntityLoadEvent(loadContext);
fireEvent(afterLoadEvent)
return afterLoadEvent.getResult();


Учитывая что ивент може менять свое состояние, например loadPreventedб или afterLoadEvent.getResult, а так же модифицировать контекст, то тут логичнее будут мидлвари

$pipeline->execute($entity, $loadContext, fn($entity,loadContext)=>$this->loadData($context))
---------------------------------------------

1. Есть два результат получения данных, назвем из partial и notPartial. Это указывается в LoadContext (или детектится автоматом, опишу ниже)
1.1. notPartial - это реальные сущности которые можно сохранить, которые зареганы в uow
1.2. partial - это Ghost обьект, по сути та же сущность  с засечеными полями через рефлексию, и не зареганы в uow

2. Работа View
2.1. Если notPartial, строится запрос на выборку, где учитываются только singleValue рилейшены, т.к. доктрина умеет работать только с ними (ocramius MultiStage Hydration, решение уже не работает, но описывает проблему). Тоесть в таком случае не будут вытягиватся поля toMany. Будет только один запрос
2.2. Если partial, то будет построен план запросов, и и вытянутся сначала все singleValue рилейшены, потом отдельно все hasMany как описано в view и потом все склеится согласно струтуры связей.

3. Интернал работа с View
2.1. Если notPartial, то тут все просто, выбираем из View только те связи которые singleValue и выполняем запрос, гидрацией результата в обьекты и регистрацией в uow занимается доктрина
2.2. Если partial:
2.2.1. Строим план (количество пунктов = количеству toMany связей + 1)
2.2.2. Выполняем какждый пункт плана, в каждом пункте указана парентПропертя относительно корневого обьекта, куда записать результат.
2.2.3. Тут мы не можем использовать гидрацию доктрины в сущность, как с случае с notPartial, т.к. она регает стейт в uow а там нельзя установить hasMany связи.
2.2.4. Потому мы должны получить все как массив, создать Ghost обьект, и засетить данные.
2.3 В этом случае у нас будут 2 гидрации, одна из запроса в массив, вторая из массива в обьекты.

4. Резолв partial и notPartial.
По скольку для полноценной работы сущности нужны как минимум все поля одной таблицы, мы можем проверить
1. Если в контексте указано partialResult(false) -- то в таком случае мы всегда возвращаем сущность, и выбираем из view только singleValue связи. п.2.1.
2. Если в контексте указано partialResult(true) (по дефолту), то смотрим:
  1. Если в view указаны все поля которые нужны для сущности (по сути все поля в таблице), то возвращать notPartial (так же возможно указать singleValue связи).
  2. Иначе возвращать partial


Вопросы по гидрации partial.
1. Гидрация массив. У нас есть возможность создать кастомный select согласно view, и записать карту алиасов и к каким пропертям какой алиас относится (аналог ResultSetMapping), и потом с помощью карты установить те свойства которые вытянуты. Но, этам штука нам нужна только для того, что б можно было обойти временный рестрикшен доктрины, на создания partial обьектов, ибо он deprecated. Итого у нас есть такие пути реализации
1.1. Это делать свою гидрацию в массив по карте. Но, как появится решение от доктрины как это обрабатывать надо будет это все вытирать и переписать по новой согласно рекомендации, ибо мы рискуем остатся на текущей версии ОРМ. Минус в том, что надо городить свой велосипед.
1.2. Не указывать конкретные поля для выборки, а выбирать всю сущность, в таком случае можно использовать ArrayHydrator от доктрины, который воссоздаст структуру массива согласно рилешенов. (На следущей гидрации мы можем засетить в обьект только те поля которые указаны в View). Минус тут в том, что выборка будет всегда происходить по всем полям.
2. Гидрация в обьект, есть два варианта гидрации
2.1. После каждого пункта плана гидрировать результат пункта в обьекты, и устанавливать в рут обьект уже обьекты.
2.2. После каждого пунта результат будет в массиве, а значит и рут выборка тоже будет массивом, и устанваливать результаты склейки в массив. Затем  когда получим весь массив, тоесть когда пройдут все пункты, из готового массива создать обьекты


---------------------------------


Struct
-----------------

EntityService ->
	read -> parse conditions, parse orderby, create default query, create LoadContext
DataManager -> Add constraints to loadContext, resolve storage, call method from storage, бере констрейнти з контексту, і додає свої можливо в залежності від группи, хз.
Storage -> Fetch Data (Check contraints) will FireEvents that will check constraints in loadContext (implement as middlewares)

LoadContext.type -> willBe COUNT or READ
SaveContext.type -> Can be CREATE or UPDATE



CrudEntityContext -> Used In EntityService - Провіряє чи є взагалі доступ до моделі, але не використовує констрейнти з контексту, так само використовується в дата сторі, через івент



Convert View to FetchGroup in the JmixEclipseLinkQuery


DataStore + LoadContext
If no query in LoadContext - create a new query;

If query exists
if select only from one entity apply Fetch Group

На якому этапі фільтрувати view? Здається що view ніяк не фільтрується на вході (Які поля описапні в view такі і витягуються), можливо зроблено так, щоб не заруінити _instanceName .. бо якщо він складається з field1, field2 .. а вони заборонені, то не получиться сформувати _instanceName, або є логіка в коді завязана на ці поля, якщо їх заборонити то вона відвалиться. Получається що view фільтрується на єтапі серіалізації?? Саме так і є, є такий атрибут як DO_NOT_SERIALIZE_DENIED_PROPERTY
Що передавати в LoadContext View чи FecthGroup? - Переадється View (Бо там є наслідування, потім викликається білдер в JPaDataStore і будується план згідно вью)

LoadContext.partialLoading
По замовчуванню false
Якщо встановлено true то загружається ParialEntity
Якщо встановлена в false то загружається Entity (В такому випадку view не приміняється)

Якщо в views є всі поля які потрібні для ентіті, в куба порівнюється view=_base, а в \Doctrine\ORM\Mapping\ClassMetadataInfo::$fieldMappings, то загружається повноцінна ентіті


Jmix provides three built-in fetch plans for each entity:

_local fetch plan includes all local attributes (immediate attributes that are not references). Дивись в \Doctrine\ORM\Mapping\ClassMetadataInfo::$fieldMappings

_instance_name fetch plan includes all attributes forming the instance name. These can be local attributes and references. If instance name is not specified for an entity, this fetch plan is empty.

_base fetch plan includes all attributes of the _local and _instance_name fetch plans. It differs from _local only if the _instance_name fetch plan includes reference attributes.


----------------- LoadContextQuery.

setCondition ... добавляется всегда к основному запросу, грубо говоря скоуп



------------------- Query Executor
If partial requested (i.e. as array) then we shhould pass FetchGroup into Query, after that we should create a proxies
If !partial we don't need to do any operation and should fetch entity as is



------------------ Doctrine Fetch Plan Calculator
Fetch Group
$fetchGroup->addAttribute('id');
$fetchGroup->addAttribute('status');
$fetchGroup->addAttribute('client.id');
$fetchGroup->addAttribute('client.name');
$fetchGroup->addAttribute('client.contacts.email'); //o2m
$fetchGroup->addAttribute('client.actions');//o2m
$fetchGroup->addAttribute('lines.id');
$fetchGroup->addAttribute('lines.product.name');
$fetchGroup->addAttribute('lines.product.tags.name');


date
client
   name
lines
  product
    tags

Plan
select: date, client.name | from: orderClass | join: client | batches: lines.product.tags

select: product from OrderLines

https://wiki.eclipse.org/EclipseLink/Development/Incubator/Extensions/FetchPlan#Usage_Examples

FetchPlan must be converted to FetchGroup


Если указана связь toOne то вытягивается не значение ключа, а массив с айдишником.

-----------------------------

В першій ітерації можна проігнорити витягування конкретних полів. Витягуєм всі поля моделі, додаєм джоіни якщо вони описані в View,
а далі на етапі view фільтруєм і формуєм поля для виводу.
Це буде самий простий варіант. Напевно?
--------------------------------

Вирішення O+1
-------------------------------
Наприклад є
Orders -> m2o -> categories -> m2m category_tags
Orders -> m2m -> tags
Orders -> o2m -> lines

0. Витягнути дані для Сollection
1. Ввитягуємо всі рілейшини з Collection<Orders> as mainCollection
1.1. tags, lines, categories
2. Проходимо витягуємо ід для рілейшинів, з умовою їх зворотньої гідрації
2.1. m2m [tags] => [...[orderId=>orderId]] - Де взяти результат для зворотньої гідрації???
2.2. o2m [lines] => [...[orderId=>orderId]] - результат для зворотньоъ гідрації є в вибірці
2.3. m2o [categories] => [...[orderId=>categoryId]] - результат для зворотньоъ гідрації є в вибірці + в масиві мапінгу
3. Якщо є дані з попереднього кроку, витягуємо всі записи потрібні для рілейшинів
2.1 tags = select * from order_tags join tags where order_tags.order_id IN (orderIds);
2.2 lines = select * from lines where order_tags.order_id IN (orderIds);
2.3 categories = select * from categories where id IN (category_ids);
4. Проганяємо оримані колекції з п.0
5. Гідруємо результат в mainCollection

Варіант для юай
----------------------------------
1. Всі ендпоінти вертають дані, в ключі data
2. Якщо є запит на форму, тобто доданий параметр ?form то дані вертаються разом з формою. Де форма вертається в ключі form,
і складається з поля schema, key де key  уніклаьний ключ форми .... а як взнати до запиту з даними чи є в нас форма, чи ні?
2.1. Можна спробувати зробити роутер по формам, тобто
GET /entityName/ - list_entityName
GET /entityName/1/ - update_entityName
2.2. Нам можуть бути потрібні форми для. ХМ це дії з ресурсами, і самі форми повинні з ними взаємодіяти
LIST  - GET /entityName/
CREATE - POST /entityName/
UPDATE - POST /entityName/{id}
DELETE - DELETE /entityName/{id}


2.4. Ще один самий простий варіант, це зробити ендпоінт форм
тобто
LIST /forms/?query=/entityName/&action=LIST - вертає ідентифікатор форми.
CREATE /forms/?query=/entityName/&action=CREATE
UPDATE /forms/?query=/entityName/{id}&action=UPDATE
DELETE /forms/?query=/entityName/{id}&action=DELETE

/forms/schema?query=/entityName/&action=LIST - буде вертати саму схему форми, і ідентифікатор


3. Походу нафігація має відбуватись через ендпоінт форм, оскільки тільки форма знає як відправити/отримати дані
тоді може бути /ui/{entityName}/{id}/{action}

--- Як працюють констрейнти Constraint

Основним класом для їх перевірки є AccessManager
Всі констренти прив'язуються до контексту.
Якщо дуже грубо то це массив
[
 SomeAccessContext::class => [
  $oneConstraintInstance,
  $twoConstraintInstance,
 ]
 SomeAccessContext2::class => [
  $oneConstraintInstance3,
  $twoConstraintInstance4,
 ]
]

В залежності від контексту який підтримує кожен констрейнт вони запускаються

Тобто AccessManager::apply викликається з двума параметрами, контекст для якого потрібно застосувати констренйти, і масив констрейнтів
В залежності від классу контексту, застосуються ті констрейнти для якого вони зроблені
Приклад дерева викликів
1. DataManager::load($loadContext) -
  Додасть констрейнти до $loadContext які є обов'язковими для DataManager (тобто сам менеджер може мати свої якісь констренйти)
2. AbstractDataStore::load($loadContext)
  2.1. Цей метод запустить івент $event = new BeforeEntityLoadEvent($loadContext);
  2.2. Для цього івента є лістенер DataStoreCrudListener.beforeEntityLoad
    Тут створюється $crudContext = new CrudEntityContext($event->loadContext->getMetaClass())
    Далі викликається AccessManager::apply($crudContext, $event->loadContext->getConstraints())
    Тобто застосуються всі констрейнти для CrudEntityContext
    результати роботи констрейнтів вони запишуть в сам $crudContext
    Де в залежності від результату встановиться пропертя $event.isReadPermitted
  2.3. if($event.isReadPermitted === false) return null
  2.4. Далі викликається AbstractDataStore::loadOne($loadContext); // По факту завантаження моделі з бази
  2.5. Далі викликається createQuery($loadContext)
  2.5.1. Де створюється новий контекст $queryContext = new ReadEntityQueryContext
  2.5.2. accessManager.applyConstraints($queryContext, $loadContext::getAccessConstraints());
  2.5.2.1. Де для ReadEntityQueryContext додадутся якісь обов'язкові умови для завантаження данних (наприклад базуючись на ролі). Наприклад треба додати умову, де будуть відображатись тільки пости автора
  2.6. Далі запускається івент $event = EntityLoadingEvent($loadContext,$entity)
  2.7.1 Лістенери цього івенту, маю змогу модифікувати список $entity якы повертаються,
        тобто в випадку з однією моделлю, вони можуть або забрати її або залишити
        Це самий івент використовується і для завантаження декількох моделей, тому можливо які потрібно видалити з списку, при певних умовах
  2.8. $resultEntity = $event::getResultEntity();
  2.9. Далі запускається івент AfterEntityLoadEvent $event = EntityLoadingEvent($loadContext,$entity) (він подібний до EntityLoadingEvent)
  * Різниця між EntityLoadingEvent і AfterEntityLoadEvent це те що в оригіналі EntityLoadingEvent виконується в транзакції
---------------------- InstanceName
1. Використання на классі
#[InstanceName(properties:['firstName','lastName')]
2. Використання на проперті
class Client{
   #[InstanceName('Client name: %s']
   public string $name;
}
3. Використання на методі (в перспективі, можна підтримувати параметри методу, куди будуть прокидуватись параметри з контейнера
class Client{
   #[InstanceName]
   public function getClientName():string
   {
        return ($this->gender==='F'?'Ms. ':'Mr. ').$this->name;
   }
}

Шаблон опційний по замовчуванню він для класу буде
#[InstanceName(properties:['firstName','lastName')] - "%s %s"
Для методу і проперті буде "%s"






----------------- Examples

LoadContext<User> context = LoadContext.create(User.class).setQuery(
 LoadContext.createQuery("select u from sec$User u where u.login like :login")
 .setParameter("login", "a%")
 .setMaxResults(10))
 .setView("user.browse");
 List<User> users = dataManager.loadList(context);

 https://docs.jmix.io/jmix/data-access/data-manager.html

 ------------------------ FetchPlan builder

 fetchPlans.builder(Order.class)
.addFetchPlan(FetchPlan.BASE)
.add("orderLines", FetchPlan.BASE, FetchMode.UNDEFINED)
.add("orderLines.product", FetchPlan.BASE, FetchMode.UNDEFINED)

----- В DataStore треба використовувати ViewBuilder бо треба додати системні поля, хоча хз по факту це ж раніше мало б бути
(new View())->addProperty('lines.product');
При (new View())->addProperty('lines.product') буде вертатись ['lines']['product'] = 'uuid';
При (new View())->addProperty('lines.product',View::BASE) буде вертатись ['lines']['product'] = Product base struct;
