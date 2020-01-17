#!/usr/bin/python3
# -*- coding: utf-8 -*-

# pip3 install requests PyExecJS
# linux下同时需要nodejs环境或其他JS Runtime
import execjs
import sys, getopt
import re, requests, json

def getWaybillNo ():
    # sys.argv[1:]表示取索引1之后的值, 0为文件名
    # c: 表示短选项 -c 后面应有参数
    # code= 表示长选项 --code 后应有参数
    # options为分析出的格式信息，args为不属于格式信息的剩余参数
    options, args = getopt.getopt(sys.argv[1:], 'c:', ['code='])
    for option, value in options:
        if option in ('-c', '--code'):
            return value

def getClassName ():
    home = 'https://t.17track.net/zh-cn'
    res = requests.get(home)

    return re.findall(r'<li id="jcHeaderInput" class="(.+?)">', res.text)[0]

def main ():
    js = sys.path[0] + '/track.js'
    with open(js, 'r', encoding = 'utf-8') as f:
        track_js = f.read()
        data = '{"data":[{"num":"%s","fc":0,"sc":0}],"guid":"","timeZoneOffset":-480}' % getWaybillNo()
        ctx = execjs.compile(track_js)

        return ctx.call('get_cookie', data, getClassName())

print(main())
