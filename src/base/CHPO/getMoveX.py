# -*- coding: utf-8 -*-
# @see https://www.zhangshengrong.com/p/2EaE0b9b1M/
# @see http://ex2tron.wang/opencv-python-template-matching/

# pip install opencv-python
import cv2
import time
import json
import base64
import requests
import numpy as np

def request():
    # 毫秒时间戳
    now = int(round(time.time() * 1000))
    requestUrl = 'http://yjcx.chinapost.com.cn/qps/showPicture/verify/slideVerifyLoad'
    params = {'t': str(now)}

    return requests.get(requestUrl, params).json()

def base64ToArray(string):
    binary = base64.b64decode(string)
    # uint8是无符号八位整型，表示范围[0, 255]的整数
    return np.frombuffer(binary, np.uint8) 

def reread(img):
    img_encode = cv2.imencode('.jpg', img)[1]
    binary = np.array(img_encode).tostring()
    img = np.frombuffer(binary, np.uint8)

    return cv2.imdecode(img, cv2.IMREAD_COLOR)

def main(origin, cut):
    # @see https://segmentfault.com/q/1010000016688755/a-1020000016692956
    _cut = cv2.imdecode(cut, cv2.IMREAD_GRAYSCALE)
    _cut = reread(_cut)
    _cut = cv2.cvtColor(_cut, cv2.COLOR_BGR2GRAY)
    _cut = abs(255 - _cut)
    _cut = reread(_cut)

    _origin = cv2.imdecode(origin, cv2.IMREAD_GRAYSCALE)
    _origin = reread(_origin)

    # result = cv2.matchTemplate(_cut, _origin, cv2.TM_CCOEFF)
    result = cv2.matchTemplate(_cut, _origin, cv2.TM_CCOEFF_NORMED)
    minLoc = cv2.minMaxLoc(result)[2]

    return minLoc[0]

response = request()
origin = base64ToArray(response['YYPng_base64'])
cut = base64ToArray(response['CutPng_base64'])
moveX = main(origin, cut)

print(json.dumps({'moveX': moveX, 'uuid': response['uuid']}))
