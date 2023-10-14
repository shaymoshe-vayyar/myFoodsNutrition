import foodsdicParsing
import DBClasses

def readItems(itemsUrlParsingDB):
    DBItemsNut = DBClasses.DBItemsNutClass()
    for itemIdx in range(len(itemsUrlParsingDB.index)):
        tableNut = foodsdicParsing.ParseAndFormat(itemsUrlParsingDB.loc[itemIdx]['FoodsDBNameOrUrl'])
        DBItemsNut.AddNutsListPerItem(itemsUrlParsingDB.loc[itemIdx]['FoodName'], tableNut)

    return DBItemsNut
