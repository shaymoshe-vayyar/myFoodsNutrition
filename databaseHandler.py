import csv
import pandas as pd

__DBItemsFileName__ = r'C:\Users\ShayMoshe\OneDrive - vayyar.com\Documents\Personal\MyTestsFoodApp\DBItems.json'

def LoadItemsListToRead():
    titles =                    ['FoodsDBNameOrUrl']
    itemsDB = pd.DataFrame({
        'עגבניה'            :   ['https://www.foodsdictionary.co.il/Products/1/%D7%A2%D7%92%D7%91%D7%A0%D7%99%D7%94'],
        'מלפפון'            :   ['https://www.foodsdictionary.co.il/Products/1/%D7%9E%D7%9C%D7%A4%D7%A4%D7%95%D7%9F%20%D7%A2%D7%9D%20%D7%A7%D7%9C%D7%99%D7%A4%D7%94']
    },
    index = titles)
    return itemsDB

def StoreItemsNutDatabase(DBItems : pd.DataFrame, fileName=None):
    jsonTxt = DBItems.to_json()
    if fileName is None:
        fileName = __DBItemsFileName__
    with open(fileName,'w') as f:
        f.write(jsonTxt)

def LoadItemsNutDatabase(fileName=None):
    if fileName is None:
        fileName = __DBItemsFileName__
    DBItems = pd.read_json(fileName)
    return DBItems

