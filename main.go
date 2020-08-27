package main

import (
	"strconv"

	_ "net/http"

	"github.com/gin-gonic/gin"
	"github.com/jinzhu/gorm"
	_ "github.com/mattn/go-sqlite3"
)

// Person テーブル定義
type Person struct {
	gorm.Model
	Name string
	Age  int
}

// アンスコは使うなと言われる
func dbInit() {
	db, err := gorm.Open("sqlite3", "test.sqlite3")
	if err != nil {
		panic("failed to connect database\n")
	}

	db.AutoMigrate(&Person{})

	defer db.Close()
}

func create(name string, age int) {
	db, err := gorm.Open("sqlite3", "test.sqlite3")
	if err != nil {
		panic("failed to connect database")
	}
	db.Create(&Person{Name: name, Age: age})
}

func getAll() []Person {
	db, err := gorm.Open("sqlite3", "test.sqlite3")
	if err != nil {
		panic("failed to connect database")
	}
	var people []Person
	db.Find(&people)
	return people
}

func main() {
	r := gin.Default()
	r.LoadHTMLGlob("templates/*")
	dbInit()
	r.GET("/", func(c *gin.Context) {
		people := getAll()
		c.HTML(200, "index.tmpl", gin.H{
			"people": people,
		})
	})
	r.POST("/new", func(c *gin.Context) {
		name := c.PostForm("name")
		age, _ := strconv.Atoi(c.PostForm("age"))
		create(name, age)
		c.Redirect(302, "/")
	})
	r.Run()
}
