#!/usr/bin/python3
# -*- coding: utf-8 -*-

import execjs
import sys, getopt
import re, requests, json, logging

def getOptions ():
    options, args = getopt.getopt(sys.argv[1:], 'c:v:w:', ['cookie=', 'verify_code=', 'waybill_no='])
    res = { 'cookie': '', 'verify_code': '', 'waybill_no': '' }
    for option, value in options:
        if option in ('-c', '--cookie'):
            res['cookie'] = value
        elif option in ('-v', '--verify_code'):
            res['verify_code'] = value
        else:
            res['waybill_no'] = value

    return (res['cookie'], res['verify_code'], res['waybill_no'])

def fromRegex (text, pattern):
    res = re.search(pattern, text, re.S)
    if res is not None:
        return res.group(1)
    else:
        return None

def log (e):
    log_file = '%s/error.log' % sys.path[0]
    log_format = '%(asctime)s - %(pathname)s[line:%(lineno)d] - %(levelname)s: %(message)s'
    logging.basicConfig(filename = log_file,
                        filemode = 'a',
                        format = log_format
    )
    logging.exception(e)

cookie, verify_code, waybill_no = getOptions()
request_url = 'http://ykjcx.yundasys.com/go_wsd.php'
headers = {
    'cookie': 'PHPSESSID=%s' % cookie,
    'Upgrade-Insecure-Requests': '1',
    'Host': 'ykjcx.yundasys.com',
    'Origin': 'http://ykjcx.yundasys.com',
    'Referer': 'http://ykjcx.yundasys.com/go.php',
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36',
}
data = {
    'wen':      waybill_no,
    'yzm':      verify_code,
    # 'debug':    '1',
    # 'hx':       '23',
    # 'hh':       '23',
    # 'lang':     'C',
}
response = requests.post(request_url, headers = headers, data = data)
raw = response.text

def main ():
    # 截取包含关键内容的一段
    short_pattern = r'var wyts(.*?)if\(g_s.substring'
    text = fromRegex(raw, short_pattern)
    if text is None:
        return {
            'success': False,
            'raw': raw,
            'msg': 'request error, please check the raw',
        }
    else:
        B64_pattern  = r'var B64=(.*)var g_txm'
        GS_pattern   = r'var g_s=(.*?);'
        EVAL_pattern = r'eval(.*?)var tmp_foot'
        js = '''
        function get_result()
        {{
            var B64 = {0}
            var g_s = {1}
            eval{2}
            if(g_s.substring(0,1) == '')
            {{
                return '暂无信息'
            }}
            else
            {{
                return B64.decode(g_s).split(';')
            }}
        }}
        '''.format(fromRegex(text, B64_pattern), fromRegex(text, GS_pattern), fromRegex(text, EVAL_pattern))
        try:
            ctx = execjs.compile(js)
            data = ctx.call('get_result')
            return {
                'success': True,
                'raw': raw,
                'data': data,
            }
        except Exception as e:
            log(e)
            return {
                'success': False,
                'raw': raw,
                'msg': 'execjs error, please check the error.log',
            }

print(json.dumps(main()))
