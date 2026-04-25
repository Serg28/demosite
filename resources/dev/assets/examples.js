el('dev') // to get element or elements
//add class
el('div').addClass('active')
//remove class
el('div').removeClass('active')
//toggle class
el('div').toggleClass('active')

//append something
el('div').append('<div>Hello</div>')
//prepend something
el('div').prepend('<div>Hello</div>')
//before
el('div').before('<div>Hello</div>')
//after
el('div').after('<div>Hello</div>')

//clone element
let clone = el('div').clone()


//replace html content
el('div').ihtml('<div>Hello</div>')

//replace text content
el('div').text('Hello')

//set value
el('input, textarea').val('some value')

//remove element
el('div').remove()

//add css to element
el('div').css('background','#DEDEDE');

//if element has a class
if (el('div').hasClass('active')){
  //has class
}else{
  //doesn't have
}

//remove style
removeStyle(el('div'),'color background border')

//add attribute
el('div').attr('disabled',true);

//remove attribute
el('div').removeAttr('disabled');

//get some content
let text = el('div').text()
let html = el('div').ihtml()
let value = el('input').val()

//trim string
trim('some string') /*or*/ trim(el('input').val())

//get parent of element
el('div').parent() /*or*/ el('div').parent().parent()

//get first parent with class or tag
el('div').parents('.container')

//find children in element
el('div').findIn('input') /*or you can combine it with parent() - */ el('button').parent().findIn('button').removeClass('active')

//get index of element in container
let ind = index(el('div')) /*- returns 1 for firsth element. Can be used like*/ el('div:nth-child('+ind+')').remove()

//get next element
el('button').next() //- will return next element if exists

//get prev element
el('button').prev() //- will return previous element if exists


//ANIMATIONS

//scroll to element
let parameter1 //= el('div') - can be any tag or element
let parameter2 //= duration in ms, 500
let parameter3 //= offset from top of the viewport. NOT required

scrollTo(parameter1,parameter2,parameter3)

//fade in and fade out
el('div').fadeIn(200) /*and*/ el('div').fadeOut(200) /*or*/ el('div').fadeToggle(200)

//slide down and slide up
el('div').slideDown(200) /*and*/ el('div').slideUp(200) /*or*/ el('div').slideToggle(200)

//parallax (experimental)
params = {
  direction: 'vertical or horizontal', //vertical by default
  speed: 0.5, //0.1 by default,
  type: 'transform / margin/ position', //by default transform
  transition: 0.2, //by default 0
}
el('img').parallax(params)

//get element details, like dimensions, postition, offset, layerX and many others
let details = el('div').position()
let top = details.top

//check if element is visible (doesn't have displa:none or visibility: hidden)
if (isVisible(el('div'))){
  //visible
}else{
  //invisible
}





//events with elements
event('div','click',function(selector,event){
  //selector equals to el('div')
  //example selector.addClass('active')

  //you can do something with event, like event.preventDefault()
})

//trigger
el('button').trigger('click')

//check if element is in ViewPort
if (IsInViewport(el('div'))){
  //in viewport
}else{
  //outside of viewport
}

//if you need to do something after page loading you can use method ready
ready(function(){
  //some functions
})

//each, loop for elements

el('button').each(function(element){
  element.addClass('active')
})


//ajax

//load information from remote url inside some element
el('div').load('https://site.com',function(data){
  //data will return the content
  //here you can add some functions that will work after function finish
})

//you also can get data from some section from the site
el('div').load('https://site.com#some_block',function(data){ //just add #some_block - ID of needed block
  //data will return the content
  //here you can add some functions that will work after function finish
})

//send ajax request
event('form','submit',function(selector){
  ajax(selector,{
    url:'/',
    method: 'POST',
    data: selector.serialize(), // this method get all form inputs data
    success: function(data){
      selector.trigger('reset')//will reset all fields
      //anything else after function returns success result
      //data contains content, may be json or just raw content
    },
    error: function(data){
      //will return data if error
    }
  })
})