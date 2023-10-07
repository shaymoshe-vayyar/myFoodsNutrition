import foodsdicParsing
import pandas as pd

__nutritionValues__ = 'nutritionValues'

def readItems(DBitemsToRead):
    DBItems = None
    for itemNameToRead in DBitemsToRead:
        item = foodsdicParsing.ParseAndFormat(DBitemsToRead[itemNameToRead]['FoodsDBNameOrUrl'])
        if DBItems is None:
            DBItems = pd.DataFrame({itemNameToRead: [item]}, index=[__nutritionValues__])
        else:
            DBItems.insert(0,itemNameToRead,[item])

    return DBItems
