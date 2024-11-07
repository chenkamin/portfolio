import React from 'react'
import WorkImg from '../assets/projects/workImg.jpeg'
import RealEstate from '../assets/nft.png'

const Work = () => {
    return (
        <div name="work" className="w-full md:h-screen text-gray-300 bg-[#0a192f]" >
            <div className="max-w-[1000px] mx-auto p-4 flex flex-col justify-center w-full h-full">
                <div className="pb-8">
                    <p className="text-4xl fon-bold inline border-b-4 text-gray-300 border-pink-600">Work</p>
                    <p className="py-6">Chen out some of my recent work</p>
                </div>
                {/* // grid-item */}
                <div
                    className=" grid sm:grid-cols-2 md:grid-cols-2 gap-4">
                    <div style={{ backgroundImage: `url(${WorkImg})` }} className="shadow-lg shadow-[#040c16] group container rounded-md flex justify-center items-center mx-auto content-div">

                        {/* hover effect */}
                        <div className="opacity-0 group-hover:opacity-100">
                            <span className="text-2xl font-bold text-white tracking-wider">NFT Web App</span>
                            <div className="pt-8 text-center">
                                <a href="/">
                                    <button className="text-center rounded-lg px-4 py-3 m-2 bg-white text-gray-700 font-bold text-lg ">Demo</button>
                                </a>
                                <a href="/">
                                    <button className="text-center rounded-lg px-4 py-3 m-2 bg-white text-gray-700 font-bold text-lg ">Code</button>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div
                        style={{ backgroundImage: `url(${RealEstate})` }}
                        className="shadow-lg shadow-[#040c16] group container rounded-md flex justify-center items-center mx-auto content-div"
                    >
                        {/* hover effect */}
                        <div className="opacity-0 group-hover:opacity-100 w-full h-full flex flex-col justify-center items-center p-4">
                            <div className="text-2xl font-bold text-white tracking-wider text-center w-full">
                                NFT Web App
                            </div>
                            <div className="pt-8 text-center">
                                <span className="text-white text-sm px-4">
                                NFT Trading Platform MVP for Art Galleries built on the EOS blockchain. As part of a development team, I focused on building the front-end using React.
                                </span>
                            </div>
                        </div>
                    </div>


                </div>


            </div>
        </div>
    )
}

export default Work