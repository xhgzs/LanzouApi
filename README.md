## 简单概括
众所皆知，蓝奏云作为互联网网盘公司，虽说谈不上优秀，但是比某度云没会员的几kb强，
可怜的就是Pandownload大佬了，哎。。。。
缺点：非会员用户单次最多上传`100M`文件，优点就不用说了吧。
## 使用指南
[点击这里](https://github.com/xhgzs/LanzouApi/releases/download/v1.1/LanzouApi.zip)下载并上传服务器运行即可
例:`http://127.0.0.1/api.php?lz=链接&pw=密码`
参数`type=down`跳转直接下载
## 🎖️ 开发思路
1、抓包发现数据包内含有蓝奏文件直链接口<br>
2、接口信息：`https://www.lanzous.com/ajaxm.php`<br>
3、蓝奏云`猴精猴精`的，防止被人爬，居然加了注释干扰，然而这并没有什么卵用，在强大的正则面前都是渣渣，`这边发现PC端取出fn访问后，Sign处于源码中js动态添加文本，且为不一定性质，概括点说就是一会儿它添到var1中一会儿var2中`虽然还是在正则和判断面前没什么卵用，但是我就统一`UA`设置成`手机端`了，这样提交的参数Sign处于源码固定位置就好弄多了<br>
通过浏览器控制台Ctrl+Shirt+f搜索到Sign传参存在于源码中
分析参数都是不会变的，就Sign每次刷新都不一样，接下来就好办了，直接取呗
取出Sign后带着参数+pwd密码请求即出结果:
```json
{"zt":1,"dom":"https:\/\/vip.d0.baidupan.com","url":"?BGIAPlprUmNWX1ZuAjcAbAQ7V29U7QC9UtVRsQLqAbtQtFS\/Cs8AsQbcAcEFtFLDWogO4lGVVNAGL1YqXWgAcQQiADFablJqVmVWXwI\/AGUEY1djVD0AOlJlUW8CbQE2UGdUJQoxACcGbQFkBWJSalozDjNROVRwBnFWIF08ADMENABlWjdSKVYwVjICeQAxBG9XfVRpADVSYlFgAjwBMVBjVGEKYAAyBmYBZAVkUmNaNg47UT5UZwY3VmVdZwBjBGYAMlo0UmBWYlZlAm4AMwQ\/VzFUJQB4UjxRJwJ4AXVQI1RmCiUAPQY0AWoFZFJjWjIOOVE2VGYGJ1YkXWgAbARhADFaOlI3VjVWOQJjADgEbFdrVDkAOlJiUXkCeAF1UCBUPgpmAHoGdgExBT1SJFo9DjpRO1RvBjRWZl03ADcEMQBhWj5SIFZ1VnACIQA8BG9XZ1QzADJSYFFhAm0BNlBlVDsKcQAhBjkBJwVsUmJaMw4yUSBUZwY4VmldLwAwBDQAeVo2","inf":"\u6fc0\u6d3b\u4f18\u5316\u5408\u96c6.zip"}
```
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
好啦，没什么可说的了。<br>
4、代码处理逻辑有待大家优化哦，留下你的评论吧😇
## 📖 更新日志
```
2020-08-23 20:47 v1.1
修复已知问题
2020-07-28 15:40 v1.1
增加显示文件大小、上传时间、发布者等
2020-07-25 12:09 v1.0
首次发布
