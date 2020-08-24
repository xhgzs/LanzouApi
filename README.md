## 简单概括
众所皆知，蓝奏云作为互联网网盘公司，虽说谈不上优秀，但是比某度云没会员的几kb强，
可怜的就是Pandownload大佬了，哎。。。。
缺点：非会员用户单次最多上传`100M`文件，优点就不用说了吧。
## 使用指南
[点击这里](https://github.com/xhgzs/LanzouApi/releases/download/v1.1/LanzouApi-master.zip)下载并上传服务器运行即可
例:`http://127.0.0.1/api.php?lz=链接&pw=密码`
参数`type=down`跳转直接下载
## 🎖️ 开发思路
一堆废话，想学习的请耐心看完

1、抓包发现数据包内含有蓝奏文件直链接口<br>
2、接口信息：`https://www.lanzous.com/ajaxm.php`<br>
3、通过分析，（有无密码）只需取出Sign带着固定参数请求即出数据。通过浏览器控制台Ctrl+Shirt+f搜索到Sign传参存在于源码中，分析参数都是不会变的，就Sign每次刷新都不一样，接下来就好办了，直接取呗。取出Sign后带着参数+pwd密码（有密码的情况)请求即出结果:
```json
{"zt":1,"dom":"https:\/\/vip.d0.baidupan.com","url":"?BGIAPlprUmNWX1ZuAjcAbAQ7V29U7QC9UtVRsQLqAbtQtFS\/Cs8AsQbcAcEFtFLDWogO4lGVVNAGL1YqXWgAcQQiADFablJqVmVWXwI\/AGUEY1djVD0AOlJlUW8CbQE2UGdUJQoxACcGbQFkBWJSalozDjNROVRwBnFWIF08ADMENABlWjdSKVYwVjICeQAxBG9XfVRpADVSYlFgAjwBMVBjVGEKYAAyBmYBZAVkUmNaNg47UT5UZwY3VmVdZwBjBGYAMlo0UmBWYlZlAm4AMwQ\/VzFUJQB4UjxRJwJ4AXVQI1RmCiUAPQY0AWoFZFJjWjIOOVE2VGYGJ1YkXWgAbARhADFaOlI3VjVWOQJjADgEbFdrVDkAOlJiUXkCeAF1UCBUPgpmAHoGdgExBT1SJFo9DjpRO1RvBjRWZl03ADcEMQBhWj5SIFZ1VnACIQA8BG9XZ1QzADJSYFFhAm0BNlBlVDsKcQAhBjkBJwVsUmJaMw4yUSBUZwY4VmldLwAwBDQAeVo2","inf":"\u6fc0\u6d3b\u4f18\u5316\u5408\u96c6.zip"}
```
蓝奏云`猴精猴精`的，防止被爬，加了注释干扰，虽然正则可以解决，但是`这边发现PC端源码中不能直接取出Sign，Sign还处于第二个页面的链接中，也就是还得访问https://www.lanzous.com/fn?一堆类似token的数据才能搜索出需要的Sign参数，Sign参数位置处于js动态添加的一段代码中（好家伙，又加了注释干扰给正则增加难度，Sign出现的位置又是有概率的，概括点说就是一会儿它添到var1变量中一会儿var2中）`虽然正则还是可以解决，但是我就统一将`UA`设置成`手机端`了，这样提交的参数Sign就处于源码中固定位置，省去了繁琐的取数据<br>
部分代码参考：
```php
preg_match(/lanzous.com\/(.*)/,$lz,$id);
// $lz网盘链接
$lz = 'https://www.lanzous.com/tp/'.$id[1];
// 重新做个拼接，因为要重新访问tp才能下载
$context = file_get_contens($lz,$options);
// $options数据流
// 详细流程请查看代码内部
$row = json_decode($context);
// 这里解释一下，json_decode参数2为true时，可用数组形式输出结果集
// 例如:$row['dom']，我为了省事就直接对象调用了
```
4、代码处理逻辑有待大家优化哦，留下你的评论吧😇
## 📖 更新日志
```
2020-08-23 20:47 v1.1
修复已知问题
2020-07-28 15:40 v1.1
增加显示文件大小、上传时间、发布者等
2020-07-25 12:09 v1.0
首次发布
