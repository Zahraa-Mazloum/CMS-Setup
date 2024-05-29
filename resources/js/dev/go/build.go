package main

import (
	"bufio"
	"fmt"
	"os"
	"os/exec"
	"strings"
	"sync"
	"time"

	"github.com/Delta456/box-cli-maker/v2"
	"github.com/joho/godotenv"
)

func main() {
	mode := os.Args[1:][0]

	err := godotenv.Load("../../../../.env")
	if err != nil {
		output(p("red", "Error loading .env file : "+err.Error()))
	}

	projectInfo(mode)

	tiers := strings.Split(os.Getenv("TIERS"), ",")
	tiersLength := len(tiers)

	output(p("green", "Build "), p("red", strings.Join(tiers, ", ")), p("green", " as "+mode))

	var wg sync.WaitGroup
	wg.Add(tiersLength)

	max := 0
	for _, tier := range tiers {
		if len(tier) > max {
			max = len(tier)
		}
	}

	colors := [5]string{"yellow", "blue", "green", "gray", "cyan"}
	for i, tier := range tiers {
		go func(tier string, mode string, index int) {
			defer wg.Done()
			execute(tier, mode, max, colors[index])
		}(tier, mode, i)
	}

	wg.Wait()
	output(p("green", "Build Finished"))

}

func projectInfo(mode string) {
	name := p("green", os.Getenv("APP_NAME")) + " Project"
	version := p("cyan", "Version") + " : " + p("purple", os.Getenv("APP_VERSION"))
	timezone := p("cyan", "Timezone") + " : " + p("purple", os.Getenv("APP_TIMEZONE"))
	language := p("cyan", "Language") + " : " + p("purple", os.Getenv("APP_LANGUAGE"))
	url := p("cyan", "URL") + " : " + p("purple", os.Getenv("APP_URL"))
	developer := p("cyan", "Developer") + " : " + p("purple", os.Getenv("APP_DEVELOPER"))
	buildMode := p("cyan", "Mode") + " : " + p("purple", mode)

	panel(name, url+"\n"+version+"\n"+timezone+"\n"+language+"\n"+buildMode+"\n"+developer)
	fmt.Print("\n\n")
}

func output(text ...string) {
	hours, minutes, _ := time.Now().Clock()
	currUTCTimeInString := fmt.Sprintf("%02d:%02d | ", hours, minutes)
	fmt.Print(p("gray", currUTCTimeInString))
	for _, phrase := range text {
		fmt.Print(phrase)
	}
	fmt.Print("\n")
}

func p(color string, phrase string) string {
	m := make(map[string]string)
	m["red"] = "\033[31m"
	m["green"] = "\033[32m"
	m["yellow"] = "\033[33m"
	m["blue"] = "\033[34m"
	m["purple"] = "\033[35m"
	m["cyan"] = "\033[36m"
	m["gray"] = "\033[37m"
	m["white"] = "\033[97m"
	reset := "\033[0m"
	return fmt.Sprintf("%s%s%s", m[color], phrase, reset)
}

func panel(title string, text string) {
	Box := box.New(box.Config{Px: 2, Py: 1, Type: "Bold", Color: "Yellow", TitlePos: "Top", TitleColor: "Yellow", AllowWrapping: true})
	Box.Print(title, text)
}

func execute(tier string, mode string, length int, color string) {
	prefix := fmt.Sprintf("%-*s", length, tier) + p("white", " | ")

	var command string = fmt.Sprintf("vite build --config=../../../../vite.config.js --watch --force --mode %s:%s", mode, tier)
	if mode == "production" {
		command = strings.Replace(command, " --watch", "", -1)
	}

	output(p(color, prefix), p("purple", command))

	var arguments = strings.Split(command, " ")

	cmd := exec.Command("npx", arguments...)

	executeError(cmd, color, prefix)

	cmdReader, err := cmd.StdoutPipe()

	if err != nil {
		output(p(color, prefix), p("red", err.Error()))
	}
	scanner := bufio.NewScanner(cmdReader)
	go func() {
		for scanner.Scan() {
			if scanner.Err() != nil {
				output(p(color, prefix), p("purple", scanner.Err().Error()))
			} else {
				text := strings.TrimSpace(scanner.Text())
				if len(text) > 0 {
					output(p(color, prefix), p("white", text))
				}
			}
		}
	}()
	if err := cmd.Start(); err != nil {
		output(p(color, prefix), p("red", err.Error()))
	}
	if err := cmd.Wait(); err != nil {
		output(p(color, prefix), p("red", err.Error()))
	}
}

func executeError(cmd *exec.Cmd, color string, prefix string) {
	cmdReader, err := cmd.StderrPipe()

	if err != nil {
		output(p(color, prefix), p("red", err.Error()))
	}
	scanner := bufio.NewScanner(cmdReader)
	go func() {
		for scanner.Scan() {
			if scanner.Err() != nil {
				output(p(color, prefix), p("purple", scanner.Err().Error()))
			} else {
				text := strings.TrimSpace(scanner.Text())
				if len(text) > 0 {
					output(p(color, prefix), p("purple", text))
				}
			}
		}
	}()
}
