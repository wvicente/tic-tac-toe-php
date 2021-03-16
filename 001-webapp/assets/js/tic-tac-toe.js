((w,d)=>{
  'use strict';
  
  w.tttObject = {
    boardDOM: null,
    board: null,
    boardElms: null,
    player: 'o',
    adversary: 'x',

    setPlayer: function (opt) {
      if ( opt !== 'x' && opt !== 'o' ) { console.error('Invalid Option'); return false; }
      else {
        if (opt === 'x') { this.player = opt; this.adversary = 'o'; return true; }
        else { this.player = opt; this.adversary = 'x'; return true; }
      }
    },

    addMove: function (player, box) {
      if (player !== 'x' && player !== 'o') { console.error('Player Invalid'); return false; }
      else {
        if (box === 'undefined' ) { console.error('Board Position Invalid'); return false; }
        else {
          if (box.getAttribute('ttt_value') !== "" ) { console.error('Board Position Occupied'); return false; }
          else {
            box.setAttribute('ttt_value', player)
            box.innerHTML = player
            this.getBoard(this.boardDOM)
            return true;
          }
        }
      }
    },
    
    getBoard: function (_board) {
      if(_board.length === 0 ) { console.error('Board Not Found'); }
      else {
        let children = [].slice.call(_board[0].children)
        this.boardElms = children.filter(el => { return el.hasAttribute("ttt_value") });
        this.board = this.getBoardValues(this.boardElms);
      }
    },

    getBoardValues: function (arr) {
      let _board = [];
      arr = arr.map( el => { return el.getAttribute('ttt_value') } )

      for(let s=0; s<arr.length; s+=3)
        _board.push(arr.slice(s, s+3)) 

      return _board;
    },

    sendBoard: function () {
      let data = {
        board: this.board,
        player: this.player
      }
      mainTools.sendRequest(data)
    },

    loadBoadActions: function(){
      let _boardElms = []

      this.boardElms.forEach( el => {
        el.addEventListener("click", (elm) => {
          if (tttObject.addMove(tttObject.player, elm.target))
            tttObject.sendBoard()
        })
      });
    },

    loadBoard: function (_board){
      let dom_count = 0

      _board.forEach(el => {
        el.forEach(dom => {
          if(dom !== ""){
            this.boardElms[dom_count].setAttribute('ttt_value', dom)
            this.boardElms[dom_count].innerHTML = dom
          }
          dom_count++
        })
      });
      this.getBoard(this.boardDOM)
    },

    initialize: function (el) {
      this.boardDOM = el
      this.getBoard(el)
      this.loadBoadActions()
    }
  }

  tttObject.initialize(d.getElementsByClassName('board'));
  console.info('[tttObject] loaded');

})(window,document)