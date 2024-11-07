import React from 'react'

import HTML from '../assets/html.png'
import CSS from '../assets/css.png'
import JS from '../assets/javascript.png'
import ReactIcon from '../assets/react.png'
import Node from '../assets/node.png'
import AWS from '../assets/aws.png'
import Tailwind from '../assets/tailwind.png'
import Redis from '../assets/redis.png'
import PHP from '../assets/php1.png'
import Mongo from '../assets/mongo.png'
import MySQL from '../assets/sql.png'
import Laravel from '../assets/laravel.png'
import Docker from '../assets/docker.png'
import Appwrite from '../assets/appwrite.png'
import ReactLogo from '../assets/react.png'
import { useTranslation } from 'react-i18next';

const Skils = () => {
    const { t } = useTranslation();

  return (
    <div name="skills" className="w-full h-screeen bg-[#0a192f] text-gray-300">
        {/* container */}
        <div className="max-w-[1000px] mx-auto p-4 flex flex-col justify-center w-full h-full">
            <div>
                <p className="text-4xl inline border-b-4 border-pink-600  ">{t("skils-header")}</p>
                <p className="py-4 ">{t("skils-list")}</p>
            </div>

            <div className="w-full grid grid-cols-2 sm:grid-cols-4 gap-4 text-center py-8">
                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={HTML} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">HTML</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={CSS} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">CSS</p>
                </div>
                
                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={JS} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Javascript</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Node} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Node.js</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Mongo} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Mongo</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={AWS} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">AWS</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Docker} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Docker</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Laravel} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Laravel</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={MySQL} alt="html" className="w-20 mx-auto h-20" />
                    <p className="my-4">SQL</p>
                </div>
                
                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Redis} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Redis</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={Appwrite} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">Appwrite</p>
                </div>

                <div className="shadow-md shadow-[#040c16] hover:scale-110 duration-500">
                    <img src={ReactLogo} alt="html" className="w-20 mx-auto" />
                    <p className="my-4">React</p>
                </div>
                

            </div>
        </div>
    </div>
  )
}

export default Skils