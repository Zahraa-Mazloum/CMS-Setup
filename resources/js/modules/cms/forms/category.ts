import { Model } from "@/modules/form/lib/model";
import { Attrs } from "./attrs";
import { Field } from "@/modules/form/lib/field";
import { CategoryLocal } from "./cateory-local";
import { DataList } from "@/modules/form/types";

export class Category extends Model {
  get id(): Field {
    return this.field("id", "ID", this.initData?.id ?? null);
  }

  get type(): Field {
    return this.field("type", "Type", this.initData?.type ?? "default");
  }

  get parentId(): Field {
    return this.field(
      "parent_id",
      "Parent ID",
      this.initData?.parent_id ?? null
    );
  }

  get path(): Field {
    return this.field("path", "Path", this.initData?.path ?? null);
  }

  get status(): Field {
    return this.field("status", "Status", this.initData?.status ?? "active");
  }

  get position(): Field {
    return this.field("position", "Position", this.initData?.position ?? "0");
  }

  get locals(): CategoryLocal[] {
    return this.hasLocals(
      (initData: DataList) =>
        new CategoryLocal(initData, (self: any) => {
          self.type = this.initData.type ?? "default";
        })
    );
  }

  get attrs(): Attrs {
    return this.hasOne("attrs", (initData: DataList) => {
      return Attrs.generate(
        this.initData?.type ?? "default",
        "settings",
        this.initData.attrs,
        "category"
      );
    });
  }

  get rules(): any[] {
    return [];
  }
}
