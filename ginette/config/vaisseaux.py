#!/usr/bin/python
# -*- coding: utf8 -*-


VAISS = [
    { #0 -petit transporteur
        "nm":'pt',
        "tk":7,
        "ps":4000,
        "pb":10,
        "va":5,
        "fr":5000,
        "vb":5000,
        "cd":10,
        "m":2000,
        "c":2000,
        "d":0,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #1 -grand transporteur
        "nm":'gt',
        "tk":7,
        "ps":12000,
        "pb":25,
        "va":5,
        "fr":25000,
        "vb":7500,
        "cd":50,
        "m":6000,
        "c":6000,
        "d":0,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #2 -chasseur léger
        "nm":'cle',
        "tk":7,
        "ps":4000,
        "pb":10,
        "va":50,
        "fr":50,
        "vb":12500,
        "cd":20,
        "m":3000,
        "c":1000,
        "d":0,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #3 -chasseur lourd
        "nm":'clo',
        "tk":8,
        "ps":10000,
        "pb":25,
        "va":150,
        "fr":100,
        "vb":10000,
        "cd":75,
        "m":6000,
        "c":4000,
        "d":0,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #4 -croiseur
        "nm":'cr',
        "tk":8,
        "ps":27000,
        "pb":50,
        "va":400,
        "fr":800,
        "vb":15000,
        "cd":300,
        "m":20000,
        "c":7000,
        "d":2000,
        "rft":['se', 'ss', 'cle', 'lm'],
        "rfv":[5.0, 5.0, 3.0, 10.0]
    },
    { #5 -vaisseau de bataille
        "nm":'vb',
        "tk":9,
        "ps":60000,
        "pb":200,
        "va":1000,
        "fr":1500,
        "vb":10000,
        "cd":500,
        "m":45000,
        "c":15000,
        "d":0,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #6 -vaisseau de colonisation
        "nm":'vc',
        "tk":8,
        "ps":30000,
        "pb":100,
        "va":50,
        "fr":7500,
        "vb":2500,
        "cd":1000,
        "m":10000,
        "c":20000,
        "d":10000,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #7 -Recycleur
        "nm":'re',
        "tk":7,
        "ps":16000,
        "pb":10,
        "va":1,
        "fr":20000,
        "vb":2000,
        "cd":300,
        "m":10000,
        "c":6000,
        "d":2000,
        "rft":['se', 'ss'],
        "rfv":[5.0, 5.0]
    },
    { #8 -Sonde espionnage
        "nm":'se',
        "tk":7,
        "ps":1000,
        "pb":0.01,
        "va":0.01,
        "fr":5,
        "vb":1000000,
        "cd":1,
        "m":0,
        "c":1000,
        "d":0,
        "rft":[],
        "rfv":[]
    },
    { #9 -Bombardier
        "nm":'bo',
        "tk":8,
        "ps":75000,
        "pb":500,
        "va":1000,
        "fr":500,
        "vb":4000,
        "cd":1000,
        "m":50000,
        "c":25000,
        "d":15000,
        "rft":['se', 'ss', 'lm', 'lle', 'llo', 'ai'],
        "rfv":[5.0, 5.0, 20.0, 20.0, 10.0, 10.0]
    },
    { #10 -Satellite solaire
        "nm":'ss',
        "ps":2000,
        "pb":1,
        "va":1,
        "fr":0,
        "vb":0,
        "cd":0,
        "tk":0,
        "m":0,
        "c":2000,
        "d":500,
        "rft":[],
        "rfv":[]
    },
    { #11 -Destructeur
        "nm":'de',
        "tk":9,
        "ps":110000,
        "pb":500,
        "va":2000,
        "fr":2000,
        "vb":5000,
        "cd":1000,
        "m":60000,
        "c":50000,
        "d":15000,
        "rft":['se', 'ss', 'lle'],
        "rfv":[5.0, 5.0, 10.0]
    },
    { #12 -Etoile de la mort
        "nm":'rip',
        "tk":9,
        "ps":9000000,
        "pb":50000,
        "va":200000,
        "fr":1000000,
        "vb":100,
        "cd":1,
        "m":5000000,
        "c":4000000,
        "d":1000000,
        "rft":['se', 'ss', 'pt', 'gt', 'cle', 'clo', 'cr', 'vb', 'vc', 're', 'bo', 'de', 'lm', 'lle', 'llo', 'cg', 'ai'],
        "rfv":[1250.0, 1250.0, 250.0, 250.0, 200.0, 100.0, 33.0, 30.0, 250.0, 250.0, 25.0, 5.0, 200.0, 200.0, 100.0, 50.0, 100.0]
    },
    { #13 -Traqueur
        "nm":'tr',
        "tk":9,
        "ps":70000,
        "pb":400,
        "va":700,
        "fr":750,
        "vb":10000,
        "cd":250,
        "m":30000,
        "c":40000,
        "d":15000
    }
]

