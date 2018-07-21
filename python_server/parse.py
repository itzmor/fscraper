import sys
#sys.path.append('/home/itzik/.local/lib/python3.6/site-packages/lxml')
sys.path.insert(0,'/home/itzik/.local/lib/python3.6/site-packages/lxml')
import lxml
import bs4
print ("IN PY")
deli = "\n"
html = deli.join(sys.argv)
print (html)
htmls = bs4.BeautifulSoup(html, 'lxml')
alla = htmls.findAll('a')
alla = htmls.select("a[href*=friends_tab]")

#for i in alla:
#    if i.text != "":
#        print (i.text)
#print (len(alla))
