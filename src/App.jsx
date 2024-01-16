import { useState } from 'react'
import './index.css'
import NavBar from './components/NavBar'
import Home from './components/Home'
import About from './components/About'
import Skils from './components/Skils'
import Work from './components/Work'
import Contant from './components/Contant'

function App() {

  return (
    <>
    <NavBar />
    <Home />
    <About />
    <Skils />
    <Work />
    <Contant />
    </>
  )
}

export default App
