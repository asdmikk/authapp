#!/usr/bin/python3
# -*- coding: utf-8 -*-

import cgitb; cgitb.enable()
import cgi
import json
import sqlite3

print("Content-type: application/json")
print("")

form = cgi.FieldStorage()

u_id = form.getfirst('u_id')
name = form.getfirst('name')
id_code = form.getfirst('id_code')
email = form.getfirst('email')
fb_name = form.getfirst('fb_name')
gg_name = form.getfirst('gg_name')
# fb_id = form.getfirst('fb_id')
# gg_id = form.getfirst('gg_id')

result = {
    'query': {
        'name': name,
        'id_code': id_code,
        'email': email,
        'fb_name': fb_name,
        # 'fb_id': fb_id,
        'gg_name': gg_name,
        # 'gg_id': gg_id,
    },
    'persons': [],
}

conn = sqlite3.connect('../data.db')
c = conn.cursor()

if u_id:
    sql = "SELECT name, id_code, email, fb_name, fb_id, gg_name, gg_id, fb_img, gg_img, fb_email " \
          "FROM person " \
          "WHERE u_id='%s'" % u_id
else:
    sql = "SELECT name, id_code, email, fb_name, fb_id, gg_name, gg_id, fb_img, gg_img, fb_email FROM person WHERE "
    for key in result['query']:
        if result['query'][key] and str(result['query'][key]).strip() is not "":
            sql += str(key) + " LIKE '%" + str(result['query'][key]) + "%'"
            sql += " AND "
    sql = sql[:len(sql)-5] + ";"

for row in c.execute(sql):
    person = {
        'name': row[0],
        'id_code': row[1],
        'email': row[2],
        'fb_name': row[3],
        'fb_id': row[4],
        'gg_name': row[5],
        'gg_id': row[6],
        'fb_img': row[7],
        'gg_img': row[8],
        'fb_email': row[9]
    }
    result['persons'].append(person)

conn.close()

print(json.dumps(result))

