## About

一些简单物流公司抓取接口的查询整合，理论上没有请求次数限制，不保证长期有效，有时间会陆续增加

## 支持列表

✅ 代表暂时有效

🚧 代表施工中

❌ 代表失效或因有验证需要crack

| 快递公司     | 常量名          | 抓取类型       | 支持       | 添加日期      | 失效日期
| :-----:      | :-----:         | :-----:        | :-----:    | :-----:       | :-----:
| 申通         | TYPE_STO        | 简单API        | ✅         | 2019-08-19
| 圆通         | TYPE_YTO        | 简单API        | ✅         | 2019-08-19
| 中通         | TYPE_ZTO        | 简单API        | ✅         | 2019-08-19
| 百世快递     |                 | HTML正则       | 🚧
| 顺丰         |                 |                | ❌
| 韵达         |                 |                | ❌

## 安装
```sh
composer require sockball/logistics
```

## demo
```php
use sockball\logstics\Logistics;

$waybillNo = 'l';
// 圆通
$result = Logistics::getLatestTrace(Logistics::TYPE_YTO, $waybillNo);
print_r($result);
```
或
```
php test/STO_test.php
php test/YTO_test.php
php test/ZTO_test.php
```

## 方法
主类为 `Logistics`，现可用方法都为静态  
由于每次查询会保留一次单号和结果，若要连续查询同一订单最新情况，应设置 `force` 参数为 `true` （即强制发出请求查询）
```php
public static function getLatestTrace(string $type, string $waybillNo, bool $force = false)
public static function getFullTraces (string $type, string $waybillNo, bool $force = false)
public static function getOriginTrace(string $type, string $waybillNo, bool $force = false)
```

## License
[MIT](https://github.com/sockball/logistics/blob/master/LICENSE)
