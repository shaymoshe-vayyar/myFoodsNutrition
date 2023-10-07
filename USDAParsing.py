import requests
import requests_cache
from bs4 import BeautifulSoup
#from myGui import showTable
import pandas as pd


__flagChachingSite__ = True
#__foodHebName__ = 'עגבניה'
#__dictHebrewToFNDDS__ = {"עגבניה": "tomato, raw",
#                    "בצל": "onion, raw"
#                     }

__myApiKey__ = 'cWjHlCbUgkl2VQ1IS32'+'DkuRuVZdLWsrmMztIvmQu'


def ParseUrl(url):
    if __flagChachingSite__:
        session = requests_cache.CachedSession('cacheUrlName')
        r = session.get(url)
    else:
        session = requests.session()
        r = session.get(url)

    textToParse = r.text
    soup = BeautifulSoup(textToParse, 'html.parser')
    table = soup.find('table', class_='nv-table')
    nutValueTableRows = None
    for nutData in table.find_all('tr'):
        nutDataEnt = nutData.find_all('td')
        if (len(nutDataEnt) > 1):
            nutName = nutDataEnt[0].text
            # m = re.search(r'\((.*?)\)', nutName)
            nutUnits = nutName[nutName.find("(") + 1:nutName.find(")")]
            if nutName.find("("):
                nutNameWOUnits = nutName[:nutName.find("(")].strip() + (nutName[nutName.find(")") + 1:]).strip()
            else:
                nutNameWOUnits = nutName
            nutValue = nutDataEnt[1].get('data-start')
            if (nutValue is None):
                nutValue = 0
            # print("{} = {}, {}".format(nutNameWOUnits,nutValue,nutNameUnits))
            if nutValueTableRows is None:
                nutValueTableRows = pd.DataFrame({nutNameWOUnits: [nutValue, nutUnits]}, index=['nutValue', 'nutUnits'])
            else:
                nutValueTableRows.insert(0,nutNameWOUnits,[nutValue, nutUnits])
        else:
           for line in nutData.find_all('th'):
                if line.get('id')=='sizeNameTd':
                    if not line.text.find('100 גרם'):
                        print(line)
                        raise Exception('error in parsing, size is not 100 grams!')
    # showTable(['Name', 'Value', 'Units'],nutValueTableRows)
    # urlFoods = 'curl https://api.nal.usda.gov/fdc/v1/foods/search?api_key=DEMO_KEY&query=Cheddar%20Cheese'

    myFoodName = dictHebrewToFNDDS[foodHebName]  # "tomato"
    urlFoodsGet = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key={ApiKey}&query={foodName}&dataType=Survey (FNDDS)".format(
        ApiKey=myApiKey, foodName=myFoodName)
    r = requests.get(url=urlFoodsGet) # TODO: Change to cached
    # print(r)
    dictNutrientToHebrew = {"Protein": "חלבון",
                            "Total lipid (fat)": "שומנים",
                            "Carbohydrate, by difference": "פחמימות"}
    for ii in range(3):  # len(r.json().get('foods')[0])):
        nJSON = r.json().get('foods')[0].get('foodNutrients')[ii]
        nName = nJSON['nutrientName']
        nHebName = dictNutrientToHebrew[nName]
        nUnitName = nJSON['unitName']
        nValue = nJSON['value']  # TODO: Multiple by unitName
        print("גרם {}={}".format(nValue, nHebName))

    return nutValueTableRows

