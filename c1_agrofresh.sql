-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 30 2025 г., 21:06
-- Версия сервера: 10.11.11-MariaDB-deb12
-- Версия PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `c1_agrofresh`
--

-- --------------------------------------------------------

--
-- Структура таблицы `banlist`
--

CREATE TABLE `banlist` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `moderator` int(11) NOT NULL COMMENT 'Модератор',
  `expire` datetime NOT NULL COMMENT 'Дата окончания',
  `comment` mediumtext NOT NULL COMMENT 'Причина блокировки',
  `permanent` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Навсегда ли',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Активен ли бан'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком банов';

-- --------------------------------------------------------

--
-- Структура таблицы `callbacks`
--

CREATE TABLE `callbacks` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `name` varchar(50) NOT NULL COMMENT 'Имя',
  `city` varchar(50) DEFAULT NULL COMMENT 'Город',
  `phone` varchar(20) NOT NULL COMMENT 'Телефон',
  `email` varchar(100) NOT NULL COMMENT 'E-Mail',
  `message` text NOT NULL COMMENT 'Сообщение',
  `is_checked` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Просмотрено ли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица для обратной связи';

--
-- Дамп данных таблицы `callbacks`
--

INSERT INTO `callbacks` (`id`, `date`, `user`, `name`, `city`, `phone`, `email`, `message`, `is_checked`) VALUES
(1, '2025-03-13 21:52:55', 6, 'Анастасия', NULL, '89503457893', 'harchenkova@yandex.ru', 'Хочу узнать о поставщиках', 1),
(2, '2025-03-19 15:42:34', 1, 'Мария Николаевна', NULL, '88952495869', 'berlovamasha@yandex.ru', 'рпо', 1),
(3, '2025-03-23 14:05:55', 10, 'Yfczn', NULL, '8952542545', 'zhuknasytazhuk@mail.ru', 'Помогите выбрать хлеб', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `formats`
--

CREATE TABLE `formats` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `icon` varchar(50) NOT NULL COMMENT 'Имя файла иконки',
  `name` varchar(50) NOT NULL COMMENT 'Расширение',
  `about` varchar(100) NOT NULL COMMENT 'Описание формата'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком форматов файлов';

--
-- Дамп данных таблицы `formats`
--

INSERT INTO `formats` (`id`, `icon`, `name`, `about`) VALUES
(0, 'unknown.png', '', 'Неизвестный файл'),
(1, 'jpg.png', 'jpg', 'Сжатое изображение'),
(2, 'jpeg.png', 'jpeg', 'Сжатое изображение'),
(3, 'png.png', 'png', 'Оптимизированное изображение'),
(4, 'gif.png', 'gif', 'Анимированное изображение'),
(5, 'bmp.png', 'bmp', 'Несжатое изображение'),
(6, 'doc.png', 'doc', 'Документ MS Word 2003'),
(7, 'docx.png', 'docx', 'Документ MS Word'),
(8, 'xls.png', 'xls', 'Таблица MS Office 2003'),
(9, 'xlsx.png', 'xlsx', 'Таблица MS Office'),
(10, 'ppt.png', 'ppt', 'Презентация MS Office 2003'),
(11, 'pptx.png', 'pptx', 'Презентация MS Office'),
(12, 'pdf.png', 'pdf', 'Файл Adobe Reader'),
(13, 'djvu.png', 'djvu', 'Электронная книга в формате DJVU'),
(14, 'fb2.png', 'fb2', 'Электронная книга в формате FB2'),
(15, 'rtf.png', 'rtf', 'Документ WordPad'),
(16, 'txt.png', 'txt', 'Текстовый документ'),
(17, 'js.png', 'js', 'Файл JavaScript'),
(18, 'css.png', 'css', 'Файл каскадной таблицы стилей'),
(19, 'html.png', 'html', 'Файл веб-страницы'),
(20, 'php.png', 'php', 'Файл гипертекстового препроцессора'),
(21, 'zip.png', 'zip', 'Сжатый ZIP-архив'),
(22, 'rar.png', 'rar', 'Сжатый RAR-архив'),
(23, '7z.png', '7z', 'Сжатый 7z-архив'),
(24, 'tar.png', 'tar', 'Сжатый TAR-архив'),
(25, 'iso.png', 'iso', 'Образ диска'),
(26, 'exe.png', 'exe', 'Исполняемый файл Windows'),
(27, 'mp3.png', 'mp3', 'Сжатый звуковой файл'),
(28, 'wav.png', 'wav', 'Несжатый звуковой файл'),
(29, 'wma.png', 'wma', 'Звуковой файл Windows Media'),
(30, 'wmv.png', 'wmv', 'Видеофайл Windows Media'),
(31, 'avi.png', 'avi', 'Видеофайл AVI'),
(32, 'mp4.png', 'mp4', 'Сжатый видеофайл'),
(33, 'sql.png', 'sql', 'Файл скрипта базы данных'),
(34, 'max.png', 'max', 'Файл 3Ds Max'),
(35, 'accdb.png', 'accdb', 'Файл базы данных MS Access'),
(36, 'mdb.png', 'mdb', 'Файл базы данных MS Access 2003'),
(37, 'bak.png', 'bak', 'Файл резервной копии'),
(38, 'mkv.png', 'mkv', 'Видеофайл Matroshka'),
(39, 'dll.png', 'dll', 'Подключаемая библиотека приложений Windows'),
(40, 'ini.png', 'ini', 'Файл конфигурации'),
(41, 'cfg.png', 'cfg', 'Файл конфигурации'),
(42, 'conf.png', 'conf', 'Файл конфигурации'),
(43, 'svg.png', 'svg', 'Файл векторного изображения'),
(44, 'swf.png', 'swf', 'Flash-ролик'),
(45, 'psd.png', 'psd', 'Файл изображения Adobe Photoshop'),
(46, 'fla.png', 'fla', 'Файл проекта Adobe Flash'),
(47, 'flac.png', 'flac', 'Звуковой файл со сжатием без потерь'),
(48, 'vob.png', 'vob', 'Видеофайл для DVD'),
(49, 'flv.png', 'flv', 'Видеофайл Adobe Flash'),
(50, '3gp.png', '3gp', 'Видеофайл с мобильного телефона'),
(51, 'amr.png', 'amr', 'Звуковой файл с диктофона'),
(52, 'cdr.png', 'cdr', 'Файл проекта Corel Draw'),
(53, 'ico.png', 'ico', 'Файл иконки'),
(54, 'mov.png', 'mov', 'Видеофайл MOV'),
(55, 'mpg.png', 'mpg', 'Видеофайл MPG'),
(56, 'ogg.png', 'ogg', 'Аудиофайл OGG'),
(57, 'odt.png', 'odt', 'Документ OpenOffice Writer'),
(58, 'odp.png', 'odp', 'Презентация OpenOffice Impress'),
(59, 'csv.png', 'csv', 'Таблица OpenOffice Calc'),
(60, 'odb.png', 'odb', 'База данных OpenOffice Base'),
(61, 'dat.png', 'dat', 'Файл сериализованных данных'),
(62, 'tiff.png', 'tiff', 'Изображение высокого разрешения'),
(63, 'cab.png', 'cab', 'Файл Windows Cabinet'),
(64, 'tif.png', 'tif', 'Изображение высокого разрешения'),
(65, 'bat.png', 'bat', 'Консольный исполняемый файл'),
(66, 'htm.png', 'htm', 'Файл веб-страницы'),
(67, 'inf.png', 'inf', 'Файл условий установки'),
(68, 'sys.png', 'sys', 'Системный файл'),
(69, 'vsd.png', 'vsd', 'Проект MS Visio'),
(70, 'gzip.png', 'gz', 'Сжатый GZIP-архив'),
(71, 'bin.png', 'bin', 'Бинарный файл данных'),
(72, 'midi.png', 'midi', 'Формат цифрового интерфейса музыкальных инструментов'),
(73, 'cad.png', 'cad', 'Файл проекта AutoCad'),
(74, 'java.png', 'java', 'Исходный код Java'),
(75, 'aac.png', 'aac', 'Звуковой файл AAC'),
(76, 'epub.png', 'epub', 'Файл электронной книги ePub'),
(77, 'xcf.png', 'xcf', 'Многослойное изображение Gimp XCF'),
(78, 'pdn.png', 'pdn', 'Многослойное изображение Paint.NET PDN'),
(79, 'ttf.png', 'ttf', 'Файл пользовательского шрифта'),
(80, 'msi.png', 'msi', 'Пакет установщика Windows');

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name` varchar(50) NOT NULL COMMENT 'Имя языка',
  `prefix` varchar(3) NOT NULL COMMENT 'Префикс языка (буквенный код)',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Является ли стандартным'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком языков';

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`id`, `name`, `prefix`, `is_default`) VALUES
(1, 'Русский', 'ru', 1),
(2, 'English', 'en', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT 'Имя модуля',
  `pointer` varchar(100) NOT NULL COMMENT 'Указатель на модуль',
  `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Активен ли модуль'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком модулей движка';

--
-- Дамп данных таблицы `modules`
--

INSERT INTO `modules` (`id`, `name`, `pointer`, `is_active`) VALUES
(1, 'Магазин', 'shop', 1),
(2, 'Новости', 'news', 1),
(3, 'Поиск', 'search', 0),
(4, 'Отзывы', 'feedbacks', 0),
(5, 'Обратная связь', 'callback', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата создания',
  `u_date` datetime DEFAULT NULL COMMENT 'Дата изменения',
  `user` int(11) NOT NULL COMMENT 'Автор',
  `title` varchar(100) NOT NULL DEFAULT 'Без названия' COMMENT 'Заголовок',
  `text` mediumtext DEFAULT NULL COMMENT 'Текст',
  `views` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Количество просмотров',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Публична ли новость'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица для новостей';

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `date`, `u_date`, `user`, `title`, `text`, `views`, `is_public`) VALUES
(1, '2025-03-30 20:18:35', '2025-03-30 20:55:09', 1, 'Аграрии сообщили о рисках снижения качества зерна', 'Сельское хозяйство - едва ли не единственная отрасль российской экономики, которая стабильно росла даже на фоне санкций. Несколько лет назад доходы от экспорта сельхозпродукции впервые в истории страны превысили аналогичную статью доходов \"оборонки\". Однако последние два года стали для села непростыми: наложились друг на друга такие факторы, как сложности во внешней торговле, относительно высокая инфляция и рост себестоимости продукции. Аналитики считают, что новый сельскохозяйственный сезон будет одним из самых сложных за последние 10-15 лет.\r\n\r\nВ этом году общая посевная площадь прогнозируется выше прошлогодней - почти 84 миллиона гектаров, из них 55,8 миллиона гектаров - яровые. В прошлом году общая посевная площадь в России составляла 82,6 миллиона гектаров, в этом году ведомство планирует нарастить этот показатель до 83,7 миллиона гектаров.\r\n\r\nКак подчеркнула министр сельского хозяйства Оксана Лут, в 2025 году предполагается расширение посевов масличных культур, сахарной свеклы, а также овощей, картофеля и кормовых растений.\r\n\r\nАгропромышленный комплекс планомерно входит в этап весенних полевых работ. В некоторых регионах России уже начаты работы по подкормке озимых. А на юге страны стартовал яровой сев.\r\n\r\n[b]В сухом остатке[/b]\r\n\r\nВ начале марта на юге России стартовала весенняя полевая кампания, начался сев яровых культур. На поля вышли тысячи тракторов. Обеспеченность аграриев семенами, сельхозтехникой и топливом практически 100-процентная, следует из оперативных данных статистики. Однако ситуация с урожаем тревожная, отмечают опрошенные фермеры.\r\n\r\nОсновной урожай пшеницы (главная экспортная культура) дают озимые, которые сеют осенью. Так вот в прошлом году сентябрь выдался довольно сухим. Многие аграрии были вынуждены ждать дождей и, не дождавшись, сеять в сухую землю. Конечно, засуха не везде. Но в ЮФО, СКФО и ПФО, которые дают существенную часть урожая пшеницы, ситуация в этом плане была напряженной. И зима тоже выдалась скупой на осадки.\r\n\r\nМинистр сельского хозяйства Оксана Лут ранее сообщила на оперативном штабе о подготовке к весенним полевым работам, что 87 процентов озимых культур находятся в хорошем и удовлетворительном состоянии. Зимний период 2024/25 года запомнится аномально малым количеством осадков на большей части европейской территории России. Это обстоятельство существенно повлияло на состояние озимых посевов, которые, несмотря на относительно мягкую погоду, вступили в весну с серьезным дефицитом влаги.\r\n\r\nНа это обратили внимание даже в Совете Федерации. Недостаток снежного покрова в ряде регионов России может привести к дефициту влаги для сельскохозяйственных культур во время весенне-полевых работ 2025 года, предупредил глава Комитета СФ по аграрно-продовольственной политике и природопользованию Александр Двойных.\r\n\r\n\"Слава богу, что не было сильных морозов. Иначе без снежного покрова у нас бы вымерзли все посевы, - говорит фермер Роман Алейников. - Но сейчас в почве наблюдается дефицит влаги. И озимым нужна вода для вегетации, и яровым культурам, чтобы зерно проросло. Не скажу, что ситуация критическая, но определенный дискомфорт есть\".\r\n\r\n\"К посевной они готовятся активно, - рассказывает независимый эксперт Александр Корбут. - Основное для нее уже закупили осенью, но из-за капризов природы в некоторых регионах придется пересевать значительные площади. Это дополнительные затраты. У многих на это может не оказаться денег\", - считает он.\r\n\r\nДелать прогнозы относительно урожая-2025 пока рано. Вполне вероятно, валовой сбор будет ниже рекордных прошлых лет. Это нормально. Однако эксперты опасаются, что из-за снижения количества внесенных удобрений и агрохимии российские сельхозпроизводители в ближайшие два-три года столкнутся со снижением валового сбора и снижением качества зерна.\r\n\r\nАграрии и правда были вынуждены снижать объемы внесения удобрений и агрохимии из-за экономической ситуации. В первую очередь из-за повышения себестоимости средств защиты растений, удобрений и прочих необходимых \"комплектующих\". Особенно это заметно у мелких производителей - фермеров, у которых нет финансовой подушки безопасности и которые сильнее всего реагируют на изменение рыночной конъюнктуры. Один-два сезона никак не скажутся на почве. За последние \"жирные\" годы аграрии вносили довольно много необходимых веществ, этого ресурса хватит.\r\n\r\nНо если не вносить удобрения или вносить их в дефицитном объеме два-три сезона подряд, то через несколько лет мы можем увидеть \"яму\" - просадку в эффективности, из которой выбираться будет сложно, предупреждают аналитики.\r\n\r\n', 59, 1),
(2, '2025-03-30 20:56:39', NULL, 1, 'Как правительство сдерживает рост цен на продукты', 'Правительство оперативно реагирует на ситуацию с ценами, вводит меры по сокращению экспорта и увеличению импорта, чтобы не допустить резких скачков, дает субсидии производителям продуктов питания. Об этом рассказал глава кабмина Михаил Мишустин в ходе отчета в Госдуме.\r\n\r\n\"Перед отчетом мы проводили встречи с фракциями, и практически на всех были вопросы, которые особенно волнуют людей. И среди них, конечно, рост цен\", - рассказал он и перечислил меры по недопущению резкого подорожания продуктов питания.\r\n\r\n\"Главным было поддержать отечественных производителей\", - рассказал премьер. Для повышения рентабельности предприятий кабмин сохранил все основные направления поддержки АПК, среди них - финансирование племенного животноводства и элитного семеноводства, глубокой переработки зерна и молока. Аграриям предоставляли льготные кредиты на посевную и уборку урожая.\r\n\r\n\"По продукции, где невозможно было достичь быстрого результата, принимали оперативные меры и временно ограничивали вывоз, чтобы товары оставались внутри страны, а не уходили за рубеж. В итоге интересы российских потребителей защитили. Так было по зерновым культурам, по подсолнечному маслу\", - рассказал Мишустин.\r\n\r\nА где нужно было дополнительно насытить рынок, временно расширяли ввоз продуктов из дружественных стран, обнулили пошлины на импорт сливочного масла, мяса, картофеля, моркови, яблок.\r\n\r\nГлава правительства отметил здесь роль Федеральной антимонопольной службы (ФАС). Она выявила необоснованный рост наценок на ряд товаров - до 50%. Так было с рыбной продукцией, где цены для потребителя в итоге повысились в 3,5 раза.\r\n\r\n\"Надо еще активнее взаимодействовать со всеми игроками рынка и, если необходимо, принимать самые жесткие меры\", - считает Мишустин.\r\n\r\nОн также напомнил, что правительство специально предоставило регионам серьезные инструменты и возможности для совместной работы с производителями, поставщиками и торговыми сетями по ограничению цен и наценок.', 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `news_images`
--

CREATE TABLE `news_images` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `news_id` bigint(20) NOT NULL COMMENT 'ID сообщения',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `file_name` varchar(100) NOT NULL COMMENT 'Настоящее имя',
  `size` bigint(20) NOT NULL COMMENT 'Размер',
  `hash` varchar(128) NOT NULL COMMENT 'Хэш SHA512',
  `height` int(11) NOT NULL DEFAULT 0 COMMENT 'Высота',
  `width` int(11) NOT NULL DEFAULT 0 COMMENT 'Ширина',
  `description` varchar(100) DEFAULT NULL COMMENT 'Краткое описание',
  `is_cover` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Обложка',
  `is_duplicate` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Дубликат или оригинал'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с файлами новостей' ROW_FORMAT=COMPACT;

--
-- Дамп данных таблицы `news_images`
--

INSERT INTO `news_images` (`id`, `news_id`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `description`, `is_cover`, `is_duplicate`) VALUES
(1, 1, 1, '83TMeOnLxMht2kFhvBh9Ir0gccC7jr.jpg', 74996, 'c089a2b7c7cbcae2622ee4d031d463949e75f490bac17bc071b71c89228d66db00cb1876bdb207e19109814ec6664516b9aad22a8f564dcb2a03b56b18cea3ab', 414, 620, 'Нет описания', 1, 0),
(2, 2, 1, '0MmwCPGn9-xEOqJuFNw8Tw5TBl5ID0.jpg', 240262, '463a58d65bbe047671184d86bdf86c50ad3913ca20f61b59317893f31c3dbc5842b66ad24014ae5375091a1c96874e343d0f2b70f5d2e176d3f9afe411ccd8d9', 667, 1000, 'Нет описания', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата добавления',
  `u_date` datetime DEFAULT NULL COMMENT 'Дата изменения',
  `title` varchar(100) NOT NULL COMMENT 'Заголовок',
  `content` mediumtext NOT NULL COMMENT 'Содержимое',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Публична ли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со страницами сайта';

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` (`id`, `date`, `u_date`, `title`, `content`, `is_public`) VALUES
(0, '2025-02-21 00:00:00', NULL, 'Главная', '	Добро пожаловать!\r\n	Если вы видите эту запись, значит, Team-Tech Web Engine работает. Это пример главной страницы из таблицы БД \"pages\". ID = 0.\r\n	Вы всегда можете отредактировать текст главной страницы в админке или через phpMyAdmin.', 1),
(1, '2025-02-21 00:00:00', '2025-03-30 19:38:04', 'Контакты', '	[url=/callback]Обратная связь[/url]\r\n\r\n	[b]Телефон:[/b]\r\n		+7 (999) 888-77-66\r\n\r\n	[b]Мы располагаемся по адресу:[/b]\r\n		305001, Россия, Курская обл., г. Курск, ул. Дзержинского, 47А\r\n\r\n[center]<iframe src=\"https://yandex.ru/map-widget/v1/-/CHR1iP63\" width=\"95%\" height=\"450\" frameborder=\"0\"></iframe>[/center]', 1),
(2, '2025-03-30 19:34:01', NULL, 'О нас', 'Добро пожаловать в наш магазин сельскохозяйственной продукции!\r\n\r\nМы предлагаем свежие и качественные товары прямо от фермеров — овощи, фрукты, мясо, молоко и многое другое. Всё натуральное, без лишних добавок.\r\n\r\nНаша цель — поддерживать здоровье клиентов и помогать развивать сельское хозяйство. Надёжность, свежесть и забота о вас — это мы!', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `pages_images`
--

CREATE TABLE `pages_images` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `page_id` bigint(20) NOT NULL COMMENT 'ID сообщения',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `file_name` varchar(100) NOT NULL COMMENT 'Настоящее имя',
  `size` bigint(20) NOT NULL COMMENT 'Размер',
  `hash` varchar(128) NOT NULL COMMENT 'Хэш SHA512',
  `height` int(11) NOT NULL DEFAULT 0 COMMENT 'Высота',
  `width` int(11) NOT NULL DEFAULT 0 COMMENT 'Ширина',
  `description` varchar(100) DEFAULT NULL COMMENT 'Краткое описание',
  `is_duplicate` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Дубликат или оригинал'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с файлами страниц' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name` varchar(20) NOT NULL COMMENT 'Наименование'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком привилегий';

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Пользователь'),
(2, 'Модератор'),
(3, 'Администратор');

-- --------------------------------------------------------

--
-- Структура таблицы `roles_list`
--

CREATE TABLE `roles_list` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `role` int(11) NOT NULL DEFAULT 1 COMMENT 'Роль пользователя'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица привилегий пользователей';

--
-- Дамп данных таблицы `roles_list`
--

INSERT INTO `roles_list` (`id`, `user`, `role`) VALUES
(1, 0, 1),
(2, 0, 3),
(3, 1, 1),
(4, 1, 3),
(5, 2, 1),
(6, 3, 1),
(7, 4, 1),
(8, 5, 1),
(9, 6, 1),
(10, 7, 1),
(11, 8, 1),
(12, 9, 1),
(13, 10, 1),
(14, 11, 1),
(15, 12, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `date_format` int(11) NOT NULL DEFAULT 2 COMMENT 'Формат даты/времени',
  `site_lang` int(11) NOT NULL DEFAULT 1 COMMENT 'Язык сайта',
  `site_name` varchar(100) NOT NULL COMMENT 'Имя сайта',
  `site_delimiter` varchar(5) NOT NULL COMMENT 'Разделитель заголовка',
  `site_template` int(11) NOT NULL DEFAULT 1 COMMENT 'Шаблон сайта',
  `site_enabled` tinyint(1) NOT NULL COMMENT 'Активен ли сайт',
  `inactive_message` mediumtext NOT NULL COMMENT 'Сообщение при отключённом сайте'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица настроек сайта';

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`date_format`, `site_lang`, `site_name`, `site_delimiter`, `site_template`, `site_enabled`, `inactive_message`) VALUES
(2, 1, 'АгроФреш', '|', 2, 1, 'Сайт остановлен для проведения технических работ. Пожалуйста, зайдите позже.');

-- --------------------------------------------------------

--
-- Структура таблицы `shop_cart`
--

CREATE TABLE `shop_cart` (
  `ID` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата добавления',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `item_id` bigint(20) NOT NULL COMMENT 'Товар',
  `count` int(11) NOT NULL DEFAULT 1 COMMENT 'Количество'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с корзинами пользователей';

--
-- Дамп данных таблицы `shop_cart`
--

INSERT INTO `shop_cart` (`ID`, `date`, `user`, `item_id`, `count`) VALUES
(1, '2025-03-30 20:52:26', 1, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_categories`
--

CREATE TABLE `shop_categories` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата создания',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `name` varchar(100) NOT NULL DEFAULT 'Без названия' COMMENT 'Имя',
  `description` text DEFAULT NULL COMMENT 'Описание',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Публична ли категория'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица категорий товаров';

--
-- Дамп данных таблицы `shop_categories`
--

INSERT INTO `shop_categories` (`id`, `date`, `user`, `name`, `description`, `is_public`) VALUES
(1, '2025-03-30 20:31:27', 1, 'Молочные продукты', 'Молочные продукты', 1),
(2, '2025-03-30 20:31:39', 1, 'Мясные продукты', 'Мясные продукты', 1),
(3, '2025-03-30 20:32:33', 1, 'Растительные продукты', 'Растительные продукты', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_items`
--

CREATE TABLE `shop_items` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата добавления',
  `u_date` datetime DEFAULT NULL COMMENT 'Дата редактирования',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `category` int(11) DEFAULT NULL COMMENT 'Категория (если есть)',
  `subcategory` int(11) DEFAULT NULL COMMENT 'Подкатегория (если есть)',
  `name` varchar(100) NOT NULL DEFAULT 'Без названия' COMMENT 'Имя',
  `description` text DEFAULT NULL COMMENT 'Описание',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Цена',
  `count` int(11) NOT NULL DEFAULT 0 COMMENT 'Количество',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Опубликован ли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с товарами';

--
-- Дамп данных таблицы `shop_items`
--

INSERT INTO `shop_items` (`id`, `date`, `u_date`, `user`, `category`, `subcategory`, `name`, `description`, `price`, `count`, `is_public`) VALUES
(1, '2025-03-30 20:51:44', NULL, 1, 1, 1, 'Молоко', 'Фермерский продукт\r\n\r\nПолностью натуральное, неразбавленное\r\n\r\nЦена указана за литр.', 100.00, 20, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_items_images`
--

CREATE TABLE `shop_items_images` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `item_id` bigint(20) NOT NULL COMMENT 'Товар',
  `date` datetime NOT NULL COMMENT 'Дата добавления',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `file_name` varchar(100) NOT NULL COMMENT 'Путь до файла',
  `size` bigint(20) NOT NULL COMMENT 'Размер',
  `hash` varchar(128) NOT NULL COMMENT 'Хэш',
  `height` int(11) NOT NULL COMMENT 'Высота',
  `width` int(11) NOT NULL COMMENT 'Ширина',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Изображение по умолчанию',
  `is_duplicate` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Дубликат или оригинал'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с изображениями товаров';

--
-- Дамп данных таблицы `shop_items_images`
--

INSERT INTO `shop_items_images` (`id`, `item_id`, `date`, `user`, `file_name`, `size`, `hash`, `height`, `width`, `is_default`, `is_duplicate`) VALUES
(1, 1, '2025-03-30 20:51:44', 1, 'bd547TWs4tLqGCTUt9bmqzaS2JtQUs.jpg', 164402, '9175ba776a877ba28275a5c47b69f5434794160cf5bea24cdfb47bbb9ffa85394e71ab99ea3182adaf8b65d78ee735e491dfbf3bfa40c942bd82043165a9b0ab', 1080, 1920, 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `shop_orders`
--

CREATE TABLE `shop_orders` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата формирования',
  `u_date` datetime DEFAULT NULL COMMENT 'Дата изменения (статуса или содержимого)',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `address` varchar(100) DEFAULT NULL COMMENT 'Адрес доставки',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Контактный телефон',
  `post_track` varchar(30) DEFAULT NULL COMMENT 'Номер отслеживания',
  `delivery_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Тип доставки',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Статус заказа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с заказами';

-- --------------------------------------------------------

--
-- Структура таблицы `shop_orders_parts`
--

CREATE TABLE `shop_orders_parts` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `order_id` bigint(20) NOT NULL COMMENT 'ID заказа',
  `item_id` bigint(20) NOT NULL COMMENT 'ID товара',
  `count` int(11) NOT NULL COMMENT 'Количество'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица со списком товаров в заказах';

-- --------------------------------------------------------

--
-- Структура таблицы `shop_subcategories`
--

CREATE TABLE `shop_subcategories` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата создания',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `cat_id` int(11) NOT NULL COMMENT 'ID категории',
  `name` varchar(100) NOT NULL DEFAULT 'Без названия' COMMENT 'Имя',
  `description` text DEFAULT NULL COMMENT 'Описание',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Публична ли подкатегория'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с подкатегориями товаров';

--
-- Дамп данных таблицы `shop_subcategories`
--

INSERT INTO `shop_subcategories` (`id`, `date`, `user`, `cat_id`, `name`, `description`, `is_public`) VALUES
(1, '2025-03-30 20:32:52', 1, 1, 'Молоко', 'Молоко', 1),
(2, '2025-03-30 20:33:14', 1, 1, 'Кефир', 'Кефир', 1),
(3, '2025-03-30 20:33:21', 1, 1, 'Сметана', 'Сметана', 1),
(4, '2025-03-30 20:33:29', 1, 1, 'Сыр', 'Сыр', 1),
(5, '2025-03-30 20:33:40', 1, 2, 'Свинина', 'Свинина', 1),
(6, '2025-03-30 20:33:49', 1, 2, 'Говядина', 'Говядина', 1),
(7, '2025-03-30 20:33:57', 1, 2, 'Курица', 'Курица', 1),
(8, '2025-03-30 20:34:07', 1, 2, 'Индейка', 'Индейка', 1),
(9, '2025-03-30 20:34:19', 1, 2, 'Колбасы', 'Колбасы', 1),
(10, '2025-03-30 20:34:35', 1, 3, 'Овощи', 'Овощи', 1),
(11, '2025-03-30 20:34:45', 1, 3, 'Фрукты', 'Фрукты', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name` varchar(100) NOT NULL COMMENT 'Имя',
  `pointer` varchar(50) NOT NULL COMMENT 'Указатель'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с шаблонами';

--
-- Дамп данных таблицы `templates`
--

INSERT INTO `templates` (`id`, `name`, `pointer`) VALUES
(1, 'Стандартный', 'default'),
(2, 'АгроФреш', 'agrofresh');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `date` datetime NOT NULL COMMENT 'Дата регистрации',
  `l_date` datetime DEFAULT NULL COMMENT 'Последний визит',
  `email` varchar(100) NOT NULL COMMENT 'Электронная почта',
  `login` varchar(100) NOT NULL COMMENT 'Логин',
  `pass` varchar(60) NOT NULL COMMENT 'Пароль',
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Удалён ли',
  `founder` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Основатель',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Статус профиля'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с пользователями';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `date`, `l_date`, `email`, `login`, `pass`, `deleted`, `founder`, `status`) VALUES
(0, '2018-12-01 00:00:00', NULL, '', 'System', '', 0, 1, 0),
(1, '2021-03-06 14:49:32', '2025-03-30 20:11:33', 'admin@agrofresh.eshost.ru', 'Admin', '$2y$10$HS3/UqvaEPYJe/.0dOX5zehRUD19w2QphOiM6QQCtVJwX0e6f4tHO', 0, 1, 1),
(2, '2021-05-10 21:50:57', NULL, '12345@1234.ru', '12345', '$2y$10$KqwB.6DSnFd1GvW2SAQ2Yu2Ivkl5N1gw/o9Mak0Em4bhvrR9pKSeq', 0, 0, 1),
(3, '2021-05-11 15:15:47', '2021-05-13 21:04:20', 'berlovamasha@yandex.ru', 'har', '$2y$10$RYRAS4/LQuuf/ejQT.xuv..Iaf6WUDnqOX.ExrbU1qGchu73wdbp.', 0, 0, 1),
(4, '2021-05-13 21:19:57', NULL, 'harchenkova@yandex.ru', 'harchenkova', '$2y$10$k4RZgLJ9dbFZHqDe3.mFQ.ivgC6yltBOAh3aaZji9LhQxeuxzndhi', 0, 0, 1),
(5, '2021-05-13 21:20:39', NULL, 'berlovamashad@yandex.ru', 'gdfgfd', '$2y$10$ZQfmyCocpT2ZoMm1R4P1R.5sTBINoGnqfklwqnx3jonOwUUTH.1BC', 0, 0, 1),
(6, '2021-05-13 21:22:12', '2021-05-18 16:58:48', 'berlowamar@yandex.ru', 'harchenkovaa', '$2y$10$u5DZQgg96cRWsyfy2hTf1.NsmgSocYu3e0TRigqEqvLM4XEOgDkSm', 0, 0, 1),
(7, '2021-05-18 16:00:47', NULL, 'berlovamashadf@yandex.ru', 'harchenkovaaff', '$2y$10$w7nfo2JMdyx24sxHv.W9FunAzRWyqqCuGOkWnc1adN4m92vd1wC2u', 0, 0, 1),
(8, '2021-06-21 09:59:46', NULL, 'zhuknastyazhuk@icloud.com', 'zhuk', '$2y$10$8BFeQ/tnKv9DFy5gYaGKS.aXMFe.h1fDb/JfzhJ3l/.M6yKHss7tO', 0, 0, 1),
(9, '2021-06-23 11:35:42', NULL, 'dfgbd@mail.ru', 'zhukапива', '$2y$10$dqMdV/z6Q2bD15E6mQXen.vqtYy4MnwlUPjwdNy/T7sCBaXAqPcm6', 0, 0, 1),
(10, '2021-06-23 11:37:49', '2021-06-23 14:04:29', 'zhuknastyazhuk@mail.ru', 'zhukova', '$2y$10$FuuBnu9SQf9HFlkK0X0lbenUN562gM8xpnyPoesAusEc0XiouPkwe', 0, 0, 1),
(11, '2021-06-24 10:56:41', '2021-06-24 10:58:53', 'zhukovanst@icloud.com', 'zhuk777', '$2y$10$eWNiAs8lgKclKLsBiXnkQOfb1mXRWLuYUenCRUEAo.dAQlRqFFZAu', 0, 0, 1),
(12, '2022-05-21 21:39:43', '2022-05-21 21:40:10', 'skproch@ya.ru', 'skproch', '$2y$10$8iOjGS6PmrjgKdvNb9q3YeKtqtqTJv2VJWGhghwOtldxm8HPi/3bS', 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_avatars`
--

CREATE TABLE `users_avatars` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `filename` varchar(100) NOT NULL COMMENT 'Имя файла',
  `hash` varchar(128) NOT NULL COMMENT 'Хэш SHA512',
  `is_duplicate` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Дубликат ли',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Выбран ли',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Удалён ли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с аватарами';

-- --------------------------------------------------------

--
-- Структура таблицы `users_log`
--

CREATE TABLE `users_log` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `ip` varchar(40) NOT NULL COMMENT 'IP-адрес',
  `date` datetime NOT NULL COMMENT 'Дата и время входа',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `auth_type` tinyint(1) NOT NULL COMMENT 'Тип авторизации (сайт / API)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с историей авторизации пользователей';

-- --------------------------------------------------------

--
-- Структура таблицы `users_profiles`
--

CREATE TABLE `users_profiles` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `lastname` varchar(100) DEFAULT NULL COMMENT 'Фамилия',
  `name` varchar(100) DEFAULT NULL COMMENT 'Имя',
  `patronymic` varchar(100) DEFAULT NULL COMMENT 'Отчество',
  `city` varchar(100) DEFAULT NULL COMMENT 'Город',
  `about` mediumtext DEFAULT NULL COMMENT 'О себе',
  `birthday` date DEFAULT NULL COMMENT 'Дата рождения',
  `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Пол',
  `s_year` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Показывать ли год'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с анкетами';

--
-- Дамп данных таблицы `users_profiles`
--

INSERT INTO `users_profiles` (`id`, `user`, `lastname`, `name`, `patronymic`, `city`, `about`, `birthday`, `sex`, `s_year`) VALUES
(0, 0, 'Система', 'Team-Tech.ru', 'Web Engine', '', NULL, '0000-01-01', 0, 0),
(1, 1, 'Иванченков', 'Данил', 'Алексеевич', 'Курск', 'Администратор', '2006-01-09', 1, 0),
(3, 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(4, 3, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(5, 4, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(6, 5, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(7, 6, 'Харченкова', 'Анастасия', 'Юрьевна', 'Курск', 'Обучаюсь в колледже', '2001-05-23', 2, 0),
(8, 7, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(9, 8, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(10, 9, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(11, 10, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(12, 11, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(13, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users_restores`
--

CREATE TABLE `users_restores` (
  `id` bigint(20) NOT NULL COMMENT 'ID',
  `ip` varchar(64) NOT NULL COMMENT 'IP-адрес',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `date` datetime NOT NULL COMMENT 'Дата запроса',
  `restore_key` varchar(64) NOT NULL COMMENT 'Ключ подтверждения восстановления',
  `user_agent` varchar(500) NOT NULL COMMENT 'Браузер',
  `send_count` int(11) NOT NULL DEFAULT 1 COMMENT 'Количество отправок',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Активен ли'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с ключами восстановления доступа';

-- --------------------------------------------------------

--
-- Структура таблицы `users_sessions`
--

CREATE TABLE `users_sessions` (
  `id` bigint(20) NOT NULL COMMENT 'ID сеанса',
  `date` datetime NOT NULL COMMENT 'Дата начала сеанса',
  `date_end` datetime DEFAULT NULL COMMENT 'Дата окончания сеанса',
  `user` int(11) NOT NULL COMMENT 'Пользователь',
  `ip` varchar(40) NOT NULL COMMENT 'IP-адрес',
  `sid` varchar(50) NOT NULL COMMENT 'Идентификатор сессии',
  `user_agent` varchar(200) NOT NULL COMMENT 'User-Agent',
  `is_remember` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Запомнить меня	',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Активен ли сеанс'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица с сеансами пользователей';

--
-- Дамп данных таблицы `users_sessions`
--

INSERT INTO `users_sessions` (`id`, `date`, `date_end`, `user`, `ip`, `sid`, `user_agent`, `is_remember`, `is_active`) VALUES
(1, '2025-03-30 18:45:32', '2025-03-30 20:00:25', 1, '2a03:1ac0:6dc2:3322:483e:3ef1:85d2:1274', 'TTSID-5b45d1d1af487ddb8cf70ed1d9f9e784', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 0, 0),
(2, '2025-03-30 20:02:17', '2025-03-30 20:03:21', 1, '2a03:1ac0:6dc2:3322:483e:3ef1:85d2:1274', 'TTSID-b8efb60134dffb9f0d4595a5dad6b979', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 0, 0),
(3, '2025-03-30 20:11:33', NULL, 1, '2a03:1ac0:6dc2:3322:483e:3ef1:85d2:1274', 'TTSID-eb1a665e540d87c8a1d7b9babf23b06d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 0, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `banlist`
--
ALTER TABLE `banlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `moderator` (`moderator`);

--
-- Индексы таблицы `callbacks`
--
ALTER TABLE `callbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `formats`
--
ALTER TABLE `formats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `news_images`
--
ALTER TABLE `news_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pm_id` (`news_id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pages_images`
--
ALTER TABLE `pages_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pm_id` (`page_id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `roles_list`
--
ALTER TABLE `roles_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `role` (`role`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD KEY `site_lang` (`site_lang`),
  ADD KEY `site_template` (`site_template`);

--
-- Индексы таблицы `shop_cart`
--
ALTER TABLE `shop_cart`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user` (`user`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `shop_items`
--
ALTER TABLE `shop_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategory` (`subcategory`),
  ADD KEY `category` (`category`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `shop_items_images`
--
ALTER TABLE `shop_items_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`),
  ADD KEY `item_id` (`item_id`);

--
-- Индексы таблицы `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `shop_orders_parts`
--
ALTER TABLE `shop_orders_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `shop_subcategories`
--
ALTER TABLE `shop_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat_id` (`cat_id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `users_avatars`
--
ALTER TABLE `users_avatars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users` (`user`);

--
-- Индексы таблицы `users_profiles`
--
ALTER TABLE `users_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `users_restores`
--
ALTER TABLE `users_restores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restore_key` (`restore_key`),
  ADD KEY `user` (`user`);

--
-- Индексы таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `banlist`
--
ALTER TABLE `banlist`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `callbacks`
--
ALTER TABLE `callbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `formats`
--
ALTER TABLE `formats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT для таблицы `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `news_images`
--
ALTER TABLE `news_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `pages_images`
--
ALTER TABLE `pages_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `roles_list`
--
ALTER TABLE `roles_list`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `shop_cart`
--
ALTER TABLE `shop_cart`
  MODIFY `ID` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `shop_categories`
--
ALTER TABLE `shop_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `shop_items`
--
ALTER TABLE `shop_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `shop_items_images`
--
ALTER TABLE `shop_items_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `shop_orders`
--
ALTER TABLE `shop_orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `shop_orders_parts`
--
ALTER TABLE `shop_orders_parts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `shop_subcategories`
--
ALTER TABLE `shop_subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `users_avatars`
--
ALTER TABLE `users_avatars`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `users_profiles`
--
ALTER TABLE `users_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `users_restores`
--
ALTER TABLE `users_restores`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT для таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID сеанса', AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `banlist`
--
ALTER TABLE `banlist`
  ADD CONSTRAINT `banlist_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `banlist_ibfk_2` FOREIGN KEY (`moderator`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `callbacks`
--
ALTER TABLE `callbacks`
  ADD CONSTRAINT `callbacks_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `news_images`
--
ALTER TABLE `news_images`
  ADD CONSTRAINT `news_images_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `news_images_ibfk_2` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `pages_images`
--
ALTER TABLE `pages_images`
  ADD CONSTRAINT `pages_images_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pages_images_ibfk_2` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `roles_list`
--
ALTER TABLE `roles_list`
  ADD CONSTRAINT `roles_list_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `roles_list_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`site_template`) REFERENCES `templates` (`id`),
  ADD CONSTRAINT `settings_ibfk_2` FOREIGN KEY (`site_lang`) REFERENCES `languages` (`id`);

--
-- Ограничения внешнего ключа таблицы `shop_cart`
--
ALTER TABLE `shop_cart`
  ADD CONSTRAINT `shop_cart_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `shop_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD CONSTRAINT `shop_categories_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_items`
--
ALTER TABLE `shop_items`
  ADD CONSTRAINT `shop_items_ibfk_1` FOREIGN KEY (`category`) REFERENCES `shop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_items_ibfk_2` FOREIGN KEY (`subcategory`) REFERENCES `shop_subcategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_items_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_items_images`
--
ALTER TABLE `shop_items_images`
  ADD CONSTRAINT `shop_items_images_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_items_images_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `shop_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD CONSTRAINT `shop_orders_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_orders_parts`
--
ALTER TABLE `shop_orders_parts`
  ADD CONSTRAINT `shop_orders_parts_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_orders_parts_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `shop_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_orders_parts_ibfk_3` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `shop_subcategories`
--
ALTER TABLE `shop_subcategories`
  ADD CONSTRAINT `shop_subcategories_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `shop_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `shop_subcategories_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_avatars`
--
ALTER TABLE `users_avatars`
  ADD CONSTRAINT `users_avatars_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_log`
--
ALTER TABLE `users_log`
  ADD CONSTRAINT `users_log_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_profiles`
--
ALTER TABLE `users_profiles`
  ADD CONSTRAINT `users_profiles_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_restores`
--
ALTER TABLE `users_restores`
  ADD CONSTRAINT `users_restores_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_sessions`
--
ALTER TABLE `users_sessions`
  ADD CONSTRAINT `users_sessions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
