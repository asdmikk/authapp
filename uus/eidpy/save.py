#!/usr/bin/python3
# -*- coding: utf-8 -*-

import cgitb; cgitb.enable()
import cgi
import sqlite3
import os, sys, json

print("Content-type: text/html")
print("")

form = cgi.FieldStorage()

# data_string = sys.stdin.read(int(os.environ.get('CONTENT_LENGTH', 0)))
#
# data = json.loads(data_string)

data = {
    'source': form.getfirst('source'),
    'name': form.getfirst('name'),
    'u_id': form.getfirst('u_id'),
    'img_url': form.getfirst('img_url'),
    'email': form.getfirst('email'),
    'id': form.getfirst('id')
}

# source = form.getfirst('source')
# name = form.getfirst('name')
# u_id = form.getfirst('u_id')
# img_url = form.getfirst('img_url')
# email = form.getfirst('email')
# id = form.getfirst('id')
#
#
conn = sqlite3.connect('data.db')
c = conn.cursor()

sql = "UPDATE person SET "

if data['source'] == 'google':
    sql += "gg_name='%s', gg_img='%s', gg_id='%s', email='%s' "
elif data['source'] == 'facebook':
    sql += "fb_name='%s', fb_img='%s', fb_id='%s', fb_email='%s' "

sql = sql % (data['name'], data['img_url'], data['id'], data['email'])
sql += "WHERE u_id='%s'" % data['u_id']

c.execute(sql)

conn.commit()

print(data)