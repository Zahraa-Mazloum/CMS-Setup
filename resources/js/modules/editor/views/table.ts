import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { withHeadings, content } = this.block.data

		let header = ""
		if (withHeadings === "true") {
			header = `
                <thead>
                    <tr>
                        ${content?.[0]?.map((text: string) => `<th>${text}</th>`)?.join('') ?? ""}
                    </tr>
                </thead>
            `
			content.splice(0, 1)
		}

		return `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    ${header}
                    <tbody>
                        ${content?.map((line: string[]) => `
                            <tr>${line?.map((text: string) => `<td>${text}</td>`)?.join('') ?? ""}</tr>
                        `)?.join('') ?? ""}
                    </tbody>
                </table>
            </div>
        `
	}
}
