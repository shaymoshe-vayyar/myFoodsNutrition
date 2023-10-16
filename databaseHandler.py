import csv
import pandas as pd
import mysql
import mysql.connector
from sqlalchemy import create_engine
import DBClasses
from DBClasses import *

### https://pandas.pydata.org/docs/reference/api/pandas.read_json.html
### https://pandas.pydata.org/docs/reference/api/pandas.DataFrame.index.html
### https://www.pythontutorial.net/python-basics/python-write-text-file/
### https://pythontic.com/pandas/serialization/mysql
### Link to MySql DB Management: http://localhost/phpmyadmin/index.php?route=/database/structure&server=1&db=ajax_demo&table=user
### MySql to pandas:    https://www.plus2net.com/python/pandas-mysql.php
### https://proclusacademy.com/blog/practical/pandas-read-write-sql-database/

__host__ = 'pc';
#__host__ = 'web';

class mySqlMng():
    def __init__(self):
        if (__host__ == 'pc'):
            self.__hostname__ = 'localhost';
            self.__username__ = 'root';
            self.__password__ = '';
            self.__database__ = 'ajax_demo';
        else:
            self.__hostname__ = '193.203.166.13';#'127.0.0.1';
            self.__username__ = 'u230048523_shay';
            self.__password__ = 'MosheMoshe1!';
            self.__database__ = "u230048523_ajax_demo";

        self.mydbConn = mysql.connector.connect(
            host=self.__hostname__,
            user=self.__username__,
            password=self.__password__,
            database=self.__database__
        )
        # self.mydbConn = create_engine("mysql+mysqldb://{usrid}:{password}@{localhost}/{my_db}".format(usrid=self.__username__,
        #                                                                                     password=self.__password__,
        #                                                                                     localhost=self.__hostname__,
        #                                                                                     my_db = self.__database__))

        # my_data = pd.read_sql("SELECT * FROM user", self.mydbConn)
        # print(my_data)

        # mycursor = self.mydbConn.cursor()
        # mycursor.execute("SHOW TABLES")
        # for x in mycursor:
        #     print(x)

__mySqlMng__ = mySqlMng()
# TODO: TMP Remove
# my_data = pd.read_sql("SELECT * FROM user", myDBConn.mydbConn)


__DBItemsFileName__ = r'C:\Users\ShayMoshe\OneDrive - vayyar.com\Documents\Personal\MyTestsFoodApp\DBItems.json'
__DBDailyDataFileName__ = r'C:\Users\ShayMoshe\OneDrive - vayyar.com\Documents\Personal\MyTestsFoodApp\DBDailyData.json'
__ItemNameToIdx__ = dict()

# TODO: Make a class of itemsDB
def LoadItemsListToRead():
    cols =                    ['FoodName','FoodsDBNameOrUrl']
    itemsUrlParsingDB = pd.DataFrame({
        cols[0]           :   ['עגבניה','מלפפון','חסה ערבית','צנון טרי','סלק מבושל','גזר','בצל לבן טרי','פטרוזיליה','כוסברה'],
        cols[1]           :   ['https://www.foodsdictionary.co.il/Products/1/%D7%A2%D7%92%D7%91%D7%A0%D7%99%D7%94',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%9E%D7%9C%D7%A4%D7%A4%D7%95%D7%9F%20%D7%A2%D7%9D%20%D7%A7%D7%9C%D7%99%D7%A4%D7%94',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%97%D7%A1%D7%94%20%D7%A2%D7%A8%D7%91%D7%99%D7%AA',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%A6%D7%A0%D7%95%D7%9F%20-%20%D7%A6%D7%A0%D7%95%D7%A0%D7%99%D7%AA',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%A1%D7%9C%D7%A7%20%D7%9E%D7%91%D7%95%D7%A9%D7%9C',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%92%D7%96%D7%A8',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%91%D7%A6%D7%9C',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%A4%D7%98%D7%A8%D7%95%D7%96%D7%99%D7%9C%D7%99%D7%94',
                               'https://www.foodsdictionary.co.il/Products/1/%D7%9B%D7%95%D7%A1%D7%91%D7%A8%D7%94'],
        # 'עגבניה'            :   [1,'https://www.foodsdictionary.co.il/Products/1/%D7%A2%D7%92%D7%91%D7%A0%D7%99%D7%94'],
        # 'מלפפון'            :   [2,'https://www.foodsdictionary.co.il/Products/1/%D7%9E%D7%9C%D7%A4%D7%A4%D7%95%D7%9F%20%D7%A2%D7%9D%20%D7%A7%D7%9C%D7%99%D7%A4%D7%94']
    },
    columns=cols)
    return itemsUrlParsingDB

def StoreItemsNutDatabase(DBItemsNut : DBItemsNutClass, fileName=None):
    DBItemsNut.SaveToDB(__mySqlMng__.mydbConn)

def LoadItemsNutDatabase(fileName=None):
    DBItemsNut = DBItemsNutClass()
    DBItemsNut.LoadFromDB(__mySqlMng__.mydbConn)
    return DBItemsNut

def StoreItemsDailyDatabase(DBDailyData : pd.DataFrame, fileName=None):
    jsonTxt = DBDailyData.to_json()
    if fileName is None:
        fileName = __DBDailyDataFileName__
    with open(fileName,'w') as f:
        f.write(jsonTxt)

def LoadItemsDailyDatabase(fileName=None):
    if fileName is None:
        fileName = __DBDailyDataFileName__

    try:
        DBDailyData = pd.read_json(fileName)
    except:
        DBDailyData = DailyLoggerMng.CreateNewDBDailyData()

    return DBDailyData



# __DBItemsFileName__ = r'C:\Users\ShayMoshe\OneDrive - vayyar.com\Documents\Personal\MyTestsFoodApp\DBItems.json'
# __DBDailyDataFileName__ = r'C:\Users\ShayMoshe\OneDrive - vayyar.com\Documents\Personal\MyTestsFoodApp\DBDailyData.json'
# def LoadItemsListToRead():
#     titles =                    ['FoodsDBNameOrUrl']
#     itemsDB = pd.DataFrame({
#         'עגבניה'            :   ['https://www.foodsdictionary.co.il/Products/1/%D7%A2%D7%92%D7%91%D7%A0%D7%99%D7%94'],
#         'מלפפון'            :   ['https://www.foodsdictionary.co.il/Products/1/%D7%9E%D7%9C%D7%A4%D7%A4%D7%95%D7%9F%20%D7%A2%D7%9D%20%D7%A7%D7%9C%D7%99%D7%A4%D7%94']
#     },
#     index = titles)
#     return itemsDB
#
# def StoreItemsNutDatabase(DBItems : pd.DataFrame, fileName=None):
#     jsonTxt = DBItems.to_json()
#     if fileName is None:
#         fileName = __DBItemsFileName__
#     with open(fileName,'w') as f:
#         f.write(jsonTxt)
#
# def LoadItemsNutDatabase(fileName=None):
#     if fileName is None:
#         fileName = __DBItemsFileName__
#     DBItems = pd.read_json(fileName)
#     return DBItems
#
# def StoreItemsNutDatabase(DBDailyData : pd.DataFrame, fileName=None):
#     jsonTxt = DBDailyData.to_json()
#     if fileName is None:
#         fileName = __DBDailyDataFileName__
#     with open(fileName,'w') as f:
#         f.write(jsonTxt)
#
# def LoadItemsNutDatabase(fileName=None):
#     if fileName is None:
#         fileName = __DBDailyDataFileName__
#
#     try:
#         DBDailyData = pd.read_json(fileName)
#     except:
#         DBDailyData = DailyLoggerMng.CreateNewDBDailyData()
#
#     return DBDailyData
#
