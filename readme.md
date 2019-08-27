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
| 百世快递     | TYPE_BSET       | HTML正则       | ✅         | 2019-08-27
| 丹鸟快递     |                 | 简单API        | 🚧         |
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
$logistics = Logistics::getInstance();
$result = $logistics->getLatestTrace(Logistics::TYPE_YTO, $waybillNo);
print_r($result);
```
或
```sh
git clone https://github.com/sockball/logistics.git
cd logistics

# 检测所有快递的有效性
php test/test.php

php test/STO_test.php
php test/YTO_test.php
php test/ZTO_test.php
```

返回值示例
```php
// 失败
[
    'code' => -1,
    'msg'  => '暂无信息'
]

// getLatestTrace 成功
[
    'code' => 0,
    'data' => [
        'time' => 1565369673,
        'info' => '派件已【签收】',
        'type' => '已签收'
    ]
]

// getFullTraces 成功
[
    'code' => 0,
    'data' => [
        [
            'time' => 1565369673,
            'info' => '派件已【签收】',
            'type' => '已签收'
        ],
        [
            'time' => 1565364893,
            'info' => '快件已到【xxx管家】【xxx市xxx店】,地址:xxx正门北侧xxx便民中心, 电话:18xxxxxx166',
            'type' => '已签收'
        ],
        ...
    ]
]
```

## 方法
主类为单例模式的 `Logistics`，使用时需先使用 `getInstance()` 静态方法获取实例  
由于每次查询会保留一次单号和结果，若要连续查询同一订单最新情况，应设置 `force` 参数为 `true` （即强制发出请求查询）  
以下为现有 `public` 方法
```php
public static function getInstace()
public function getLatestTrace(string $type, string $waybillNo, bool $force = false)
public function getFullTraces (string $type, string $waybillNo, bool $force = false)
public function getOriginTrace(string $type, string $waybillNo, bool $force = false)
```

## License
[MIT](https://github.com/sockball/logistics/blob/master/LICENSE)
