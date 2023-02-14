const readData = data => {
    
    if(data.art) {
        console.log(data.art)
    }
    
    if(data.comics) {
        console.log(data.comics)
    }

}

fetch("data.json")
  .then(response => response.json())
  .then(readData)

